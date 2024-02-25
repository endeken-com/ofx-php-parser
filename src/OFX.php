<?php

namespace Endeken\OFX;

use DateTime;
use DateTimeZone;
use Exception;
use SimpleXMLElement;

/**
 * Class OFX
 *
 * This class provides functions to parse OFX data and convert it into an associative array or appropriate objects.
 */
class OFX
{
    /**
     * Parse OFX data and return an associative array with the parsed information.
     *
     * @param string $ofxData The OFX data as a string.
     * @return OFXData|null An associative array with the parsed information or false on failure.
     * @throws Exception
     */
    public static function parse(string $ofxData): null|OFXData
    {

        // Check if SimpleXML object was created successfully

        $xml = OFXUtils::normalizeOfx($ofxData);
        if ($xml === false) {
            return null;
        }

        $signOn = self::parseSignOn($xml->SIGNONMSGSRSV1->SONRS);
        $accountInfo = self::parseAccountInfo($xml->SIGNUPMSGSRSV1->ACCTINFOTRNRS);
        $bankAccounts = [];

        if (isset($xml->BANKMSGSRSV1)) {
            foreach ($xml->BANKMSGSRSV1->STMTTRNRS as $accountStatement) {
                foreach ($accountStatement->STMTRS as $statementResponse) {
                    $bankAccounts[] = self::parseBankAccount($accountStatement->TRNUID, $statementResponse);
                }
            }
        } elseif (isset($xml->CREDITCARDMSGSRSV1)) {
            $bankAccounts[] = self::parseCreditAccount($xml->TRNUID, $xml);
        }
        return new OFXData($signOn, $accountInfo, $bankAccounts);
    }

    /**
     * @param SimpleXMLElement $xml
     * @return SignOn
     * @throws Exception
     */
    protected static function parseSignOn(SimpleXMLElement $xml): SignOn
    {
        $status = self::parseStatus($xml->STATUS);
        $dateTime = self::parseDate($xml->DTSERVER);
        $language = $xml->LANGUAGE;
        $institute = self::parseInstitute($xml->FI);
        return new SignOn($status, $dateTime, $language, $institute);
    }

    protected static function parseInstitute(SimpleXMLElement $xml): Institute
    {
        $name = (string) $xml->ORG;
        $id = (string) $xml->FID;
        return new Institute($id, $name);
    }

    /**
     * @param SimpleXMLElement $xml
     * @return Status
     */
    protected static function parseStatus(SimpleXMLElement $xml): Status
    {
        $code = (string) $xml->STATUS->CODE;
        $severity = (string) $xml->STATUS->SEVERITY;
        $message = (string) $xml->STATUS->MESSAGE;
        return new Status($code, $severity, $message);
    }

    /**
     * Parse a date string and return a formatted date.
     *
     * @param string $dateString The date string to parse.
     * @return DateTime The formatted date.
     * @throws Exception
     */
    protected static function parseDate(string $dateString): DateTime
    {
        $dateString = explode('.', $dateString)[0];
        // Extract the numeric part of the offset (e.g., -5 from [-5:EST])
        preg_match('/([-+]\d+):(\w+)/', $dateString, $matches);

        if (count($matches) === 3) {
            $offset = $matches[1];
            $timezoneAbbreviation = $matches[2];

            // Remove the offset with brackets and timezone abbreviation from the date string
            $dateStringWithoutOffset = preg_replace('/[-+]\d+:\w+/', '', $dateString);

            // Remove brackets and timezone abbreviation
            $dateStringWithoutOffset = str_replace(['[', ']', ':' . $timezoneAbbreviation], '', $dateStringWithoutOffset);

            // Create a DateTime object with the appropriate timezone offset
            $dateTime = new DateTime($dateStringWithoutOffset, new DateTimeZone("GMT$offset"));
            $dateTime->setTimezone(new DateTimeZone($timezoneAbbreviation));

        } else {
            // Handle cases where the date format doesn't match expectations
            // You might want to log an error or throw an exception depending on your needs
            $dateTime = new DateTime($dateString);
        }
        return $dateTime;
    }

    /**
     * @throws Exception
     */
    private static function parseBankAccount(string $uuid, SimpleXMLElement $xml): BankAccount
    {
        $accountNumber = $xml->BANKACCTFROM->ACCTID;
        $accountType = $xml->BANKACCTFROM->ACCTTYPE;
        $agencyNumber = $xml->BANKACCTFROM->BRANCHID;
        $routingNumber = $xml->BANKACCTFROM->BANKID;
        $balance = $xml->LEDGERBAL->BALAMT;
        $balanceDate = self::parseDate($xml->LEDGERBAL->DTASOF);
        $statement = self::parseStatement($xml);
        return new BankAccount(
            $accountNumber,
            $accountType,
            $agencyNumber,
            $routingNumber,
            $balance,
            $balanceDate,
            $uuid,
            $statement,
        );
    }

    /**
     * @throws Exception
     */
    private static function parseCreditAccount(string $uuid, SimpleXMLElement $xml): BankAccount
    {
        $nodeName = 'CCACCTFROM';
        if (!isset($xml->CCSTMTRS->$nodeName)) {
            $nodeName = 'BANKACCTFROM';
        }

        $accountNumber = $xml->CCSTMTRS->$nodeName->ACCTID;
        $accountType = $xml->CCSTMTRS->$nodeName->ACCTTYPE;
        $agencyNumber = $xml->CCSTMTRS->$nodeName->BRANCHID;
        $routingNumber = $xml->CCSTMTRS->$nodeName->BANKID;
        $balance = $xml->CCSTMTRS->LEDGERBAL->BALAMT;
        $balanceDate = self::parseDate($xml->CCSTMTRS->LEDGERBAL->DTASOF);
        $statement = self::parseStatement($xml);
        return new BankAccount(
            $accountNumber,
            $accountType,
            $agencyNumber,
            $routingNumber,
            $balance,
            $balanceDate,
            $uuid,
            $statement,
        );
    }

    /**
     * @throws Exception
     */
    private static function parseStatement(SimpleXMLElement $xml): Statement
    {
        $currency = $xml->CURDEF;
        $startDate = self::parseDate($xml->BANKTRANLIST->DTSTART);
        $endDate = self::parseDate($xml->BANKTRANLIST->DTEND);
        $transactions = [];
        foreach ($xml->BANKTRANLIST->STMTTRN as $transactionXml) {
            $type = (string) $transactionXml->TRNTYPE;
            $date = self::parseDate($transactionXml->DTPOSTED);
            $userInitiatedDate = null;
            if (!empty((string) $transactionXml->DTUSER)) {
                $userInitiatedDate = self::parseDate($transactionXml->DTUSER);
            }
            $amount = (float) $transactionXml->TRNAMT;
            $uniqueId = (string) $transactionXml->FITID;
            $name = (string) $transactionXml->NAME;
            $memo = (string) $transactionXml->MEMO;
            $sic = $transactionXml->SIC;
            $checkNumber = $transactionXml->CHECKNUM;
            $transactions[] = new Transaction(
                $type,
                $amount,
                $date,
                $userInitiatedDate,
                $uniqueId,
                $name,
                $memo,
                $sic,
                $checkNumber,
            );
        }
        return new Statement($currency, $transactions, $startDate, $endDate);
    }

    private static function parseAccountInfo(SimpleXMLElement $xml = null): array|null
    {
        if ($xml === null || !isset($xml->ACCTINFO)) {
            return null;
        }
        $accounts = [];
        foreach ($xml->ACCTINFO as $account) {
            $accounts[] = new AccountInfo(
                (string)$account->DESC,
                (string)$account->ACCTID
            );
        }

        return $accounts;
    }
}
