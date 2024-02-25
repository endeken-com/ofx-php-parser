<?php

namespace Endeken\OFX;

class OFXUtils
{
    public static function normalizeOfx(string $ofxContent): string|false|\SimpleXMLElement
    {
        $ofxContent = str_replace(['\r\n'], '\n', $ofxContent);
        $ofxContent = mb_convert_encoding($ofxContent, 'UTF-8', 'ISO-8859-1');
        $sgmlStart = stripos($ofxContent, '<OFX>');
        $ofxHeader = trim(substr($ofxContent, 0, $sgmlStart));
        $header = self::parseHeader($ofxHeader);
        $ofxSgml = trim(substr($ofxContent, $sgmlStart));
        if (stripos($ofxHeader, '<?xml') === 0) {
            $ofxXml = $ofxSgml;
        } else {
            if (preg_match('/<OFX>.*<\/OFX>/', $ofxSgml) === 1) {
                return str_replace('<', "\n<", $ofxSgml); // add line breaks to allow XML to parse
            }
            $ofxXml = self::convertSgmlToXml($ofxSgml);
        }
        libxml_clear_errors();
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($ofxXml);

        if ($errors = libxml_get_errors()) {
            throw new \RuntimeException('Failed to parse OFX: ' . var_export($errors, true));
        }

        return $xml;
    }

    private static function parseHeader(string $ofxHeader): array
    {
        $header = [];

        $ofxHeader = trim($ofxHeader);
        // Remove empty new lines.
        $ofxHeader = preg_replace('/^\n+/m', '', $ofxHeader);

        // Check if it's an XML file (OFXv2)
        if(preg_match('/^<\?xml/', $ofxHeader) === 1) {
            // Only parse OFX headers and not XML headers.
            $ofxHeader = preg_replace('/<\?xml .*?\?>\n?/', '', $ofxHeader);
            $ofxHeader = preg_replace(['/"/', '/\?>/', '/<\?OFX/i'], '', $ofxHeader);
            $ofxHeaderLine = explode(' ', trim($ofxHeader));

            foreach ($ofxHeaderLine as $value) {
                $tag = explode('=', $value);
                $header[$tag[0]] = $tag[1];
            }

            return $header;
        }

        $ofxHeaderLines = explode("\n", $ofxHeader);
        foreach ($ofxHeaderLines as $value) {
            $tag = explode(':', $value);
            $header[$tag[0]] = $tag[1];
        }

        return $header;
    }

    private static function convertSgmlToXml($sgml): string
    {
        $sgml = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $sgml);

        $lines = explode("\n", $sgml);
        $tags = [];

        foreach ($lines as $i => &$line) {
            $line = trim(self::closeUnclosedXmlTags($line)) . "\n";

            // Matches tags like <SOMETHING> or </SOMETHING>
            if (!preg_match("/^<(\/?[A-Za-z0-9.]+)>$/", trim($line), $matches)) {
                continue;
            }

            // If matches </SOMETHING>, looks back and replaces all tags like
            // <OTHERTHING> to <OTHERTHING/> until finds the opening tag <SOMETHING>
            if ($matches[1][0] == '/') {
                $tag = substr($matches[1], 1);

                while (($last = array_pop($tags)) && $last[1] != $tag) {
                    $lines[$last[0]] = "<{$last[1]}/>";
                }
            } else {
                $tags[] = [$i, $matches[1]];
            }
        }

        return implode("\n", array_map('trim', $lines));
    }

    private static function closeUnclosedXmlTags($line): string
    {
        // Special case discovered where empty content tag wasn't closed
        $line = trim($line);
        if (preg_match('/<MEMO>$/', $line) === 1) {
            return '<MEMO></MEMO>';
        }

        // Matches: <SOMETHING>blah
        // Does not match: <SOMETHING>
        // Does not match: <SOMETHING>blah</SOMETHING>
        if (preg_match(
            "/<([A-Za-z0-9.]+)>([\wà-úÀ-Ú0-9.\-_+, ;:\[\]'&\/\\\*()+{|}!£\$?=@€£#%±§~`\"]+)$/",
            $line,
            $matches
        )) {
            return "<{$matches[1]}>{$matches[2]}</{$matches[1]}>";
        }
        return $line;
    }

}