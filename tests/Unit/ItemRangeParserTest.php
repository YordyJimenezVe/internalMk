<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ItemRangeParserTest extends TestCase
{
    private function parseItems($inputString)
    {
        $input = $inputString;

        // Remove the word "desde" (case-insensitive)
        $input = preg_replace('/\bdesde\b/i', '', $input);

        // Normalize alternative range words like "al", "hasta", "a", or slashes "/" to a simple hyphen "-"
        $input = preg_replace('/\s*(?:\/|al|hasta|\ba\b)\s*/i', '-', $input);

        // Collapse whitespace around hyphens to simplify parsing (e.g. "10 - 15" -> "10-15")
        $input = preg_replace('/\s*-\s*/', '-', $input);

        // Convert semicolons to commas
        $input = str_replace(';', ',', $input);

        // Split by commas or whitespace (which now represent list separators)
        $tokens = preg_split('/[,\s]+/', $input);
        $tokens = array_filter(array_map('trim', $tokens));

        $itemNumbers = [];
        foreach ($tokens as $token) {
            if (str_contains($token, '-')) {
                $parts = explode('-', $token);
                if (count($parts) === 2) {
                    $startStr = trim($parts[0]);
                    $endStr = trim($parts[1]);

                    // Pattern to match optional letters prefix and digits suffix
                    if (preg_match('/^([A-Za-z]*)([0-9]+)$/', $startStr, $startMatches) &&
                        preg_match('/^([A-Za-z]*)([0-9]+)$/', $endStr, $endMatches)) {

                        $startPrefix = $startMatches[1];
                        $startNum = (int)$startMatches[2];
                        $endPrefix = $endMatches[1];
                        $endNum = (int)$endMatches[2];

                        // If the second prefix is empty, assume it inherits from the first prefix (e.g. D0120-125)
                        if (empty($endPrefix) && !empty($startPrefix)) {
                            $endPrefix = $startPrefix;
                        }

                        if ($startPrefix === $endPrefix) {
                            $padLength = strlen($startMatches[2]);
                            $step = ($startNum <= $endNum) ? 1 : -1;

                            for ($i = $startNum; ; $i += $step) {
                                $numStr = str_pad($i, $padLength, '0', STR_PAD_LEFT);
                                $itemNumbers[] = $startPrefix . $numStr;
                                if ($i == $endNum) {
                                    break;
                                }
                            }
                        } else {
                            $itemNumbers[] = $startStr;
                            $itemNumbers[] = $endStr;
                        }
                    } else {
                        $itemNumbers[] = $startStr;
                        $itemNumbers[] = $endStr;
                    }
                } else {
                    foreach ($parts as $part) {
                        $itemNumbers[] = trim($part);
                    }
                }
            } else {
                $itemNumbers[] = $token;
            }
        }

        return array_values(array_filter(array_unique($itemNumbers)));
    }

    public function test_simple_comma_list()
    {
        $this->assertEquals(['298', '294', '333'], $this->parseItems('298, 294, 333'));
    }

    public function test_semicolon_list()
    {
        $this->assertEquals(['298', '294', '333'], $this->parseItems('298; 294; 333'));
    }

    public function test_space_list()
    {
        $this->assertEquals(['298', '294', '333'], $this->parseItems('298 294 333'));
    }

    public function test_numeric_range()
    {
        $this->assertEquals(['10', '11', '12', '13', '14', '15'], $this->parseItems('10-15'));
    }

    public function test_prefixed_range()
    {
        $this->assertEquals(['D0120', 'D0121', 'D0122'], $this->parseItems('D0120-D0122'));
    }

    public function test_prefixed_short_range()
    {
        $this->assertEquals(['D0120', 'D0121', 'D0122'], $this->parseItems('D0120-122'));
    }

    public function test_spanish_range_desde_hasta()
    {
        $this->assertEquals(['10', '11', '12'], $this->parseItems('desde 10 hasta 12'));
    }

    public function test_spanish_range_al()
    {
        $this->assertEquals(['10', '11', '12'], $this->parseItems('10 al 12'));
    }

    public function test_slash_range()
    {
        $this->assertEquals(['10', '11', '12'], $this->parseItems('10/12'));
    }

    public function test_mixed_list_and_ranges()
    {
        $this->assertEquals(
            ['298', '294', '10', '11', '12', '306', 'D0001', 'D0002'],
            $this->parseItems('298, 294, 10-12, 306, D0001-2')
        );
    }
}
