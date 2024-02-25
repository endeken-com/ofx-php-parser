<?php

use PHPUnit\Framework\TestCase;
use Endeken\OFX\OFX;

class OFXTest extends TestCase
{
    private string $ofxTestFilesDir = __DIR__ . '/fixtures';

    /**
     * @throws Exception
     */
    public function testMultipleAccountsXML()
    {
        $filePath = $this->ofxTestFilesDir . '/ofx-multiple-accounts-xml.ofx';
        $ofxContent = file_get_contents($filePath);

        $parsedData = OFX::parse($ofxContent);

        var_dump($parsedData);

        $this->assertNotEmpty($parsedData);
    }

    /**
     * @throws Exception
     */
    public function testOfxData()
    {
        $filePath = $this->ofxTestFilesDir . '/ofxdata.ofx';
        $ofxContent = file_get_contents($filePath);

        $parsedData = OFX::parse($ofxContent);

        var_dump($parsedData);

        $this->assertNotEmpty($parsedData);
    }
}
