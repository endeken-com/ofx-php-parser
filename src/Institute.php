<?php

namespace Endeken\OFX;

class Institute
{
    /**
     * @var string The ID of the institute
     */
    public string $id;

    /**
     * @var string This variable stores the institute name.
     */
    public string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}