<?php
// src/Services/BarcodeService.php - Pure Core PHP Code 128 Barcode SVG Generator
// Zero external dependencies. Generates crisp, scannable vector barcodes.

namespace App\Services;

class BarcodeService
{
    /**
     * Code 128 bar/space patterns (indices 0 to 106).
     * Each pattern is 6 digits representing the module width of:
     * bar1, space1, bar2, space2, bar3, space3
     * Stop code (index 106) has 7 digits: bar1, space1, bar2, space2, bar3, space3, bar4
     */
    private static array $patterns = [
        0 => '212222', 1 => '222122', 2 => '222221', 3 => '121223', 4 => '121322',
        5 => '131222', 6 => '122213', 7 => '122312', 8 => '132212', 9 => '221213',
        10 => '221312', 11 => '231212', 12 => '112232', 13 => '122132', 14 => '122231',
        15 => '113222', 16 => '123122', 17 => '123221', 18 => '223211', 19 => '221132',
        20 => '221231', 21 => '213212', 22 => '223112', 23 => '312131', 24 => '311222',
        25 => '321122', 26 => '321221', 27 => '312212', 28 => '322112', 29 => '322211',
        30 => '212123', 31 => '212321', 32 => '232121', 33 => '111323', 34 => '131123',
        35 => '131321', 36 => '112313', 37 => '132113', 38 => '132311', 39 => '211313',
        40 => '231113', 41 => '231311', 42 => '112133', 43 => '112331', 44 => '132131',
        45 => '113123', 46 => '113321', 47 => '133121', 48 => '313121', 49 => '211331',
        50 => '231131', 51 => '213113', 52 => '213311', 53 => '213131', 54 => '311123',
        55 => '311321', 56 => '331121', 57 => '312113', 58 => '312311', 59 => '332111',
        60 => '314111', 61 => '221411', 62 => '431111', 63 => '111224', 64 => '111422',
        65 => '121124', 66 => '121421', 67 => '141122', 68 => '141221', 69 => '112214',
        70 => '112412', 71 => '122114', 72 => '122411', 73 => '142112', 74 => '142211',
        75 => '241211', 76 => '221114', 77 => '413111', 78 => '241112', 79 => '134111',
        80 => '111242', 81 => '121142', 82 => '121241', 83 => '114212', 84 => '124112',
        85 => '124211', 86 => '411212', 87 => '421112', 88 => '421211', 89 => '212141',
        90 => '214121', 91 => '412121', 92 => '111143', 93 => '111341', 94 => '131141',
        95 => '114113', 96 => '114311', 97 => '411113', 98 => '411311', 99 => '113141',
        100 => '114131', 101 => '311141', 102 => '411131', 103 => '211412', 104 => '211214',
        105 => '211232', 106 => '2331112'
    ];

    /**
     * Generate an SVG Code 128 representation of the provided string.
     * Uses Code 128 Subset B (standard ASCII) for general strings or numbers.
     *
     * @param string $text The text/AWB to encode
     * @param int $height Height of bars in pixels
     * @param float $moduleWidth Width of a single module in pixels
     * @param bool $showText Whether to display human-readable text under the barcode
     * @return string Valid SVG markup
     */
    public static function generateSvg(
        string $text,
        int $height = 55,
        float $moduleWidth = 2.0,
        bool $showText = true
    ): string {
        $text = trim($text);
        if ($text === '') {
            $text = 'EMPTY';
        }

        // Start with Code B (Pattern 104)
        $startCode = 104;
        $checksum = $startCode;
        $charIndices = [$startCode];

        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $ascii = ord($text[$i]);
            // ASCII 32 to 127 maps to values 0 to 95 in Code 128 B
            $val = $ascii - 32;
            if ($val < 0 || $val > 95) {
                $val = 0; // Replace unencodable chars with space
            }
            $charIndices[] = $val;
            $checksum += ($i + 1) * $val;
        }

        // Check digit
        $checkVal = $checksum % 103;
        $charIndices[] = $checkVal;

        // Stop code (Pattern 106)
        $charIndices[] = 106;

        // Convert sequence to binary module string (1 = bar, 0 = space)
        $modules = '';
        foreach ($charIndices as $idx) {
            $pattern = self::$patterns[$idx] ?? self::$patterns[0];
            $pLen = strlen($pattern);
            for ($p = 0; $p < $pLen; $p++) {
                $width = (int)$pattern[$p];
                $bit = ($p % 2 === 0) ? '1' : '0'; // even is bar, odd is space
                $modules .= str_repeat($bit, $width);
            }
        }

        $totalModules = strlen($modules);
        $quietZoneModules = 10;
        $totalWidth = ($totalModules + ($quietZoneModules * 2)) * $moduleWidth;
        $textHeight = $showText ? 16 : 0;
        $totalHeight = $height + $textHeight + 6;

        // Build SVG bars
        $rects = [];
        $currentX = $quietZoneModules * $moduleWidth;
        $mLen = strlen($modules);
        $barStart = null;

        for ($i = 0; $i < $mLen; $i++) {
            if ($modules[$i] === '1') {
                if ($barStart === null) {
                    $barStart = $i;
                }
            } else {
                if ($barStart !== null) {
                    $bWidth = ($i - $barStart) * $moduleWidth;
                    $bX = ($barStart + $quietZoneModules) * $moduleWidth;
                    $rects[] = sprintf(
                        '<rect x="%.2f" y="4" width="%.2f" height="%d" fill="#000000" />',
                        $bX, $bWidth, $height
                    );
                    $barStart = null;
                }
            }
        }

        if ($barStart !== null) {
            $bWidth = ($mLen - $barStart) * $moduleWidth;
            $bX = ($barStart + $quietZoneModules) * $moduleWidth;
            $rects[] = sprintf(
                '<rect x="%.2f" y="4" width="%.2f" height="%d" fill="#000000" />',
                $bX, $bWidth, $height
            );
        }

        $textElement = '';
        if ($showText) {
            $textY = $height + 17;
            $centerX = $totalWidth / 2;
            $textElement = sprintf(
                '<text x="%.2f" y="%d" text-anchor="middle" font-family="monospace, monospace" font-size="13" font-weight="600" fill="#111111" letter-spacing="2">%s</text>',
                $centerX, $textY, htmlspecialchars($text)
            );
        }

        $rectsSvg = implode("\n    ", $rects);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$totalWidth} {$totalHeight}" width="100%" height="{$totalHeight}" preserveAspectRatio="xMidYMid meet" style="display:block; margin:0 auto; max-width:{$totalWidth}px;">
    <rect width="100%" height="100%" fill="#ffffff" />
    {$rectsSvg}
    {$textElement}
</svg>
SVG;
    }
}
