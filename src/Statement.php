<?php

namespace Endeken\OFX;

use DateTime;

class Statement
{
    /**
     * @var string
     */
    public string $currency;

    /**
     * @var Transaction[]
     */
    public array $transactions;

    /**
     * @var DateTime
     */
    public DateTime $startDate;

    /**
     * @var DateTime
     */
    public DateTime $endDate;

    public function __construct(string $currency, array $transactions, DateTime $startDate, DateTime $endDate)
    {
        $this->currency = $currency;
        $this->transactions = $transactions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}