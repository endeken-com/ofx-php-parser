<?php

namespace Endeken\OFX;

use Endeken\OFX\OFXTransaction;

class OFXData
{
    /**
     * @var string
     */
    private $bankId;

    /**
     * @var string
     */
    private $accountId;

    /**
     * @var array
     */
    private $transactions = [];

    // Add more properties to represent other fields in the OFX file...

    /**
     * OFXParsedData constructor.
     *
     * @param string $bankId
     * @param string $accountId
     * @param array<OFXTransaction> $transactions
     * Add more parameters for additional fields...
     */
    public function __construct(string $bankId, string $accountId, array $transactions)
    {
        $this->bankId = $bankId;
        $this->accountId = $accountId;
        $this->transactions = $transactions;
        // Initialize other properties...
    }

    /**
     * Get the value of bankId.
     *
     * @return string
     */
    public function getBankId(): string
    {
        return $this->bankId;
    }

    /**
     * Get the value of accountId.
     *
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->accountId;
    }

    /**
     * Get the value of transactions.
     *
     * @return array<OFXTransaction>
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    // Add more getter methods for additional fields...
}