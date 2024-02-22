<?php

namespace Endeken\OFX;

use DateTime;

class OFXTransaction
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var DateTime
     */
    private $date;

    // Add more properties for transaction details...

    /**
     * OFXTransaction constructor.
     *
     * @param string $type
     * @param float $amount
     * @param DateTime $date
     * Add more parameters for additional details...
     */
    public function __construct(string $type, float $amount, DateTime $date)
    {
        $this->type = $type;
        $this->amount = $amount;
        $this->date = $date;
        // Initialize other properties...
    }

    /**
     * Get the value of type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the value of amount.
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Get the value of date.
     *
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    // Add more getter methods for additional details...
}