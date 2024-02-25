<?php

namespace Endeken\OFX;

class OFXData
{
    /**
     * @var SignOn
     */
    public SignOn $signOn;

    /**
     * @var AccountInfo[]|null
     */
    public array|null $accountInfo;

    /**
     * @var BankAccount[]
     */
    public array $bankAccounts;

    /**
     * OFXData constructor.
     *
     * @param SignOn $signOn
     * @param AccountInfo[]|null $accountInfo
     * @param BankAccount[] $bankAccounts
     */
    public function __construct(SignOn $signOn, array|null $accountInfo, array $bankAccounts)
    {
        $this->signOn = $signOn;
        $this->accountInfo = $accountInfo;
        $this->bankAccounts = $bankAccounts;
    }
}