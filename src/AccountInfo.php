<?php

namespace Endeken\OFX;

class AccountInfo
{
    /**
     * @var string $description The account description
     */
    public string $description;

    /**
     * @var string $number The account number.
     */
    public string $number;

    public function __construct(string $description, string $number)
    {
        $this->description = $description;
        $this->number = $number;
    }
}