<?php

namespace Endeken\OFX;

class Status
{
    /**
     * @var string[]
     */
    private static array $codes = [
        '0'       => 'Success',
        '2000'    => 'General error',
        '15000'   => 'Must change USERPASS',
        '15500'   => 'Signon invalid',
        '15501'   => 'Customer account already in use',
        '15502'   => 'USERPASS Lockout'
    ];

    /**
     * @var string
     */
    public string $code;

    /**
     * @var string
     */
    public string $severity;

    /**
     * @var string
     */
    public string $message;

    public function __construct(string $code, string $severity, string $message)
    {
        $this->code = $code;
        $this->severity = $severity;
        $this->message = $message;
    }

    /**
     * Get the associated code description
     *
     * @return string
     */
    public function codeDescription(): string
    {
        $code = $this->code;
        return array_key_exists($code, self::$codes) ? self::$codes[$code] : '';
    }
}