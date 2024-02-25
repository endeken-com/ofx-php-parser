<?php

namespace Endeken\OFX;

use DateTime;

class Transaction
{
    private static array $types = [
        'CREDIT' => 'Generic credit',
        'DEBIT' => 'Generic debit',
        'INT' => 'Interest earned or paid',
        'DIV' => 'Dividend',
        'FEE' => 'FI fee',
        'SRVCHG' => 'Service charge',
        'DEP' => 'Deposit',
        'ATM' => 'ATM debit or credit',
        'POS' => 'Point of sale debit or credit',
        'XFER' => 'Transfer',
        'CHECK' => 'Cheque',
        'PAYMENT' => 'Electronic payment',
        'CASH' => 'Cash withdrawal',
        'DIRECTDEP' => 'Direct deposit',
        'DIRECTDEBIT' => 'Merchant initiated debit',
        'REPEATPMT' => 'Repeating payment/standing order',
        'OTHER' => 'Other',
    ];

    /**
     * @var string
     */
    public string $type;

    /**
     * @var DateTime
     */
    public DateTime $date;

    /**
     * Date the user initiated the transaction, if known
     * @var ?DateTime
     */
    public ?DateTime $userInitiatedDate;

    /**
     * @var float
     */
    public float $amount;

    /**
     * @var string
     */
    public string $uniqueId;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $memo;

    /**
     * @var string
     */
    public string $sic;

    /**
     * @var string
     */
    public string $checkNumber;

    /**
     * Transaction constructor.
     *
     * @param string $type
     * @param float $amount
     * @param DateTime $date
     * @param ?DateTime $userInitiatedDate
     * @param string $uniqueId
     * @param string $name
     * @param string $memo
     * @param string $sic
     * @param string $checkNumber
     */
    public function __construct(
        string $type,
        float $amount,
        DateTime $date,
        ?DateTime $userInitiatedDate,
        string $uniqueId,
        string $name,
        string $memo,
        string $sic,
        string $checkNumber,
    )
    {
        $this->type = $type;
        $this->amount = $amount;
        $this->date = $date;
        $this->userInitiatedDate = $userInitiatedDate;
        $this->uniqueId = $uniqueId;
        $this->name = $name;
        $this->memo = $memo;
        $this->sic = $sic;
        $this->checkNumber = $checkNumber;
    }

    /**
     * Get the associated type description
     *
     * @return string
     */
    public function typeDescription(): string
    {
        // Cast SimpleXMLObject to string
        $type = (string)$this->type;
        return array_key_exists($type, self::$types) ? self::$types[$type] : '';
    }
}
