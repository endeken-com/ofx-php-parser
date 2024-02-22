<?php

namespace Endeken\OFX;

use DateTime;
use DateTimeZone;
use Exception;
use SimpleXMLElement;

class OFX
{
    /**
     * Parse OFX data and return an associative array with the parsed information.
     *
     * @param string $ofxData The OFX data as a string.
     * @return OFXData|false An associative array with the parsed information or false on failure.
     * @throws Exception
     */
    public static function parse(string $ofxData): false|OFXData
    {
        // Create a SimpleXML object from the OFX data
        $xml = simplexml_load_string($ofxData);

        // Check if SimpleXML object was created successfully
        if ($xml === false) {
            return false;
        }

        // Extract relevant data from the OFX XML structure and convert it to an array
        $parsedData = self::parseXML($xml);

        $ofxTransactions = [];

        foreach ($parsedData['transactions'] as $transaction) {
            $ofxTransactions[] = new OFXTransaction(
                $transaction['type'],
                $transaction['amount'],
                $transaction['date']
            );
        }

        return new OFXData($parsedData['bankId'], $parsedData['accountId'], $ofxTransactions);
    }

    /**
     * Recursively parse SimpleXML object to convert it into an associative array.
     *
     * @param SimpleXMLElement $xml SimpleXML object to parse.
     * @return array<string, mixed> Parsed data as an associative array.
     * @throws Exception
     */
    private static function parseXML(SimpleXMLElement $xml): array
    {
        $parsedData = [];

        // Check if the OFX version is specified in the header
        $version = (string) $xml->attributes()->OFXHEADER;

        if (stripos($version, '200') !== false) {
            // OFX version 2.0 or later
            $parsedData['bankId'] = (string) $xml->BANKID;
            $parsedData['accountId'] = (string) $xml->ACCTID;

            // Assuming there is a list of transactions, adjust this based on your actual structure
            $parsedData['transactions'] = [];

            foreach ($xml->STMTTRN as $transaction) {
                $parsedData['transactions'][] = self::parseTransaction($transaction);
            }
        } elseif (isset($xml->BANKID, $xml->ACCTID, $xml->STMTTRN)) {
            // OFX version 2.0 or later (inferred from structure)
            $parsedData['bankId'] = (string) $xml->BANKID;
            $parsedData['accountId'] = (string) $xml->ACCTID;

            // Assuming there is a list of transactions, adjust this based on your actual structure
            $parsedData['transactions'] = [];

            foreach ($xml->STMTTRN as $transaction) {
                $parsedData['transactions'][] = self::parseTransaction($transaction);
            }
        } elseif (isset($xml->BANKMSGSRSV1->STMTTRN)) {
            // OFX version 1.0 or earlier
            // Handle parsing logic for this version
            // Modify the code based on the actual structure of OFX 1.0
            // ...

            // Example:
            $parsedData['bankId'] = (string) $xml->BANKID;
            $parsedData['accountId'] = (string) $xml->ACCTID;

            $parsedData['transactions'] = [];

            foreach ($xml->BANKMSGSRSV1->STMTTRN as $transaction) {
                $parsedData['transactions'][] = self::parseTransaction($transaction);
            }
        } else {
            // Unable to determine the OFX version
            // You might want to log an error or throw an exception depending on your needs
            return [];
        }

        return $parsedData;
    }

    /**
     * Parse transaction details from SimpleXML.
     *
     * @param SimpleXMLElement $transaction Transaction node in SimpleXML.
     * @return array<string, mixed> Parsed transaction details as an associative array.
     * @throws Exception
     */
    private static function parseTransaction(SimpleXMLElement $transaction): array
    {
        $dateString = (string) $transaction->DTPOSTED;

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

            return [
                'type' => (string) $transaction->TRNTYPE,
                'amount' => (float) $transaction->TRNAMT,
                'date' => $dateTime,
            ];
        } else {
            // Handle cases where the date format doesn't match expectations
            // You might want to log an error or throw an exception depending on your needs
            return [];
        }
    }
}
