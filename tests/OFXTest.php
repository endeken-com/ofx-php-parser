<?php

use PHPUnit\Framework\TestCase;
use Endeken\OFX\OFX;

class OFXTest extends TestCase
{
    private string $ofxTestFilesDir = __DIR__ . '/samples';

    /**
     * @throws Exception
     */
    public function testParseSGML()
    {
        $filePath = $this->ofxTestFilesDir . '/sample_sgml.ofx';
        $ofxContent = file_get_contents($filePath);

        $parsedData = OFX::parse($ofxContent);

        // Add assertions based on your expected parsed data structure
        $this->assertNotEmpty($parsedData);
        // Add more specific assertions based on the expected structure of the parsed data
    }

    /**
     * @throws Exception
     */
    public function testParseXML()
    {
        $filePath = $this->ofxTestFilesDir . '/sample_xml.ofx';
        $ofxContent = file_get_contents($filePath);

        $parsedData = OFX::parse($ofxContent);

        // Add assertions based on your expected parsed data structure
        $this->assertNotEmpty($parsedData);
        // Add more specific assertions based on the expected structure of the parsed data
    }
}
