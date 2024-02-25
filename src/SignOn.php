<?php

namespace Endeken\OFX;

use DateTimeInterface;

class SignOn
{
    /**
     * @var Status
     */
    public Status $status;

    /**
     * @var DateTimeInterface
     */
    public DateTimeInterface $date;

    /**
     * @var string
     */
    public string $language;

    /**
     * @var Institute
     */
    public Institute $institute;

    public function __construct(Status $status, DateTimeInterface $date, string $language, Institute $institute)
    {
        $this->status = $status;
        $this->date = $date;
        $this->language = $language;
        $this->institute = $institute;
    }
}