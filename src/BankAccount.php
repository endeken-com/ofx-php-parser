<?php

namespace Endeken\OFX;

use DateTime;

class BankAccount
{
    /**
     * @var string
     */
    public string $accountNumber;

    /**
     * @var string
     */
    public string $accountType;

    /**
     * @var string
     */
    public string $balance;

    /**
     * @var DateTime
     */
    public DateTime $balanceDate;

    /**
     * @var string
     */
    public string $routingNumber;

    /**
     * @var Statement
     */
    public Statement $statement;

    /**
     * @var string
     */
    public string $transactionUid;

    /**
     * @var string
     */
    public string $agencyNumber;

    public function __construct(
        string $accountNumber,
        string $accountType,
        string $agencyNumber,
        string $routingNumber,
        string $balance,
        DateTime $balanceDate,
        string $transactionUid,
        Statement $statement,
    )
    {
        $this->accountNumber = $accountNumber;
        $this->accountType = $accountType;
        $this->agencyNumber = $agencyNumber;
        $this->routingNumber = $routingNumber;
        $this->balance = $balance;
        $this->balanceDate = $balanceDate;
        $this->transactionUid = $transactionUid;
        $this->statement = $statement;
    }
}