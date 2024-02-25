<?php

namespace Endeken\OFX;

use DateTime;

class SignOn
{
    /**
     * @var Status
     */
    public Status $status;

    /**
     * @var DateTime
     */
    public DateTime $date;

    /**
     * @var string
     */
    public string $language;

    /**
     * @var Institute
     */
    public Institute $institute;

    public function __construct(Status $status, DateTime $date, string $language, Institute $institute)
    {
        $this->status = $status;
        $this->date = $date;
        $this->language = $language;
        $this->institute = $institute;
    }
}
