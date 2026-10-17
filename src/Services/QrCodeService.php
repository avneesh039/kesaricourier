<?php
// src/Services/QrCodeService.php - Pure Core PHP QR Code Vector SVG Generator
// Zero external libraries, zero GD/ImageMagick required. Pure mathematical QR generation.

namespace App\Services;

class QrCodeService
{
    // Galois Field GF(256) tables with primitive polynomial 0x11D
    private static array $exp = [];
    private static array $log = [];
    private static bool $gfInit = false;

    // Error Correction Levels
    public const EC_LEVEL_L = 0; // 7%
    public const EC_LEVEL_M = 1; // 15%
    public const EC_LEVEL_Q = 2; // 25%
    public const EC_LEVEL_H = 3; // 30%

    // QR Code Version Specs: [total_codewords, ec_codewords_per_block, num_blocks_g1, data_cw_g1, num_blocks_g2, data_cw_g2]
    // Table for EC Level M (Standard for logistics & tracking)
    private static array $versionSpecsM = [
        1 => [26, 10, 1, 16, 0, 0],
        2 => [44, 16, 1, 28, 0, 0],
        3 => [70, 26, 1, 44, 0, 0],
        4 => [100, 18, 2, 32, 0, 0],
        5 => [134, 24, 2, 43, 0, 0],
        6 => [172, 16, 4, 27, 0, 0],
        7 => [196, 18, 4, 31, 0, 0],
        8 => [242, 22, 2, 38, 2, 39],
        9 => [292, 22, 3, 36, 2, 37],
        10 => [346, 26, 4, 43, 1, 44],
    ];

    // Alignment pattern center row/column locations per version
    private static array $alignmentCoords = [
        1 => [],
        2 => [6, 18],
        3 => [6, 22],
        4 => [6, 26],
        5 => [6, 30],
        6 => [6, 34],
        7 => [6, 22, 38],
        8 => [6, 24, 42],
        9 => [6, 26, 46],
        10 => [6, 28, 50],
    ];

    /**
     * Initialize Galois Field 256 tables.
     */
    private static function initGF(): void
    {
        if (self::$gfInit) {
            return;
        }
        $val = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$exp[$i] = $val;
            self::$log[$val] = $i;
            $val <<= 1;
            if ($val & 0x100) {
                $val ^= 0x11D;
            }
        }
        for ($i = 255; $i < 512; $i++) {
            self::$exp[$i] = self::$exp[$i - 255];
        }
        self::$gfInit = true;
    }

    private static function gMul(int $x, int $y): int
    {
        if ($x === 0 || $y === 0) {
            return 0;
        }
        return self::$exp[self::$log[$x] + self::$log[$y]];
    }

    /**
     * Compute Reed-Solomon generator polynomial of given degree.
     */
    private static function rsGeneratorPoly(int $degree): array
    {
        $poly = [1];
        for ($i = 0; $i < $degree; $i++) {
            $term = [1, self::$exp[$i]];
            $newPoly = array_fill(0, count($poly) + 1, 0);
            for ($j = 0; $j < count($poly); $j++) {
                $newPoly[$j] ^= self::gMul($poly[$j], $term[0]);
                $newPoly[$j + 1] ^= self::gMul($poly[$j], $term[1]);
            }
            $poly = $newPoly;
        }
        return $poly;
    }

    /**
     * Calculate Reed-Solomon error correction codewords.
     */
    private static function rsCalculate(array $data, int $ecCount): array
    {
        self::initGF();
        $gen = self::rsGeneratorPoly($ecCount);
        $remainder = array_merge($data, array_fill(0, $ecCount, 0));

        $dataLen = count($data);
        for ($i = 0; $i < $dataLen; $i++) {
            $factor = $remainder[$i];
            if ($factor !== 0) {
                for ($j = 0; $j < count($gen); $j++) {
                    $remainder[$i + $j] ^= self::gMul($gen[$j], $factor);
                }
            }
        }

        return array_slice($remainder, $dataLen);
    }

    /**
     * Pick smallest version (1-10) accommodating data bytes in Byte mode at EC level M.
     */
    private static function selectVersion(int $dataByteCount): int
    {
        foreach (self::$versionSpecsM as $ver => $spec) {
            $cap = ($spec[2] * $spec[3]) + ($spec[4] * $spec[5]);
            // Byte mode overhead: 4-bit mode indicator + 8-bit character count indicator (16-bit for v10+)
            $headerBits = ($ver <= 9) ? (4 + 8) : (4 + 16);
            $maxDataBytes = (int)(($cap * 8 - $headerBits) / 8);
            if ($dataByteCount <= $maxDataBytes) {
                return $ver;
            }
        }
        return 10; // Fallback max supported version
    }

    /**
     * Encode raw string into bitstream using 8-bit Byte mode.
     */
    private static function encodeData(string $text, int $version): array
    {
        $dataBytes = array_values(unpack('C*', $text));
        $byteCount = count($dataBytes);

        $spec = self::$versionSpecsM[$version];
        $totalDataCodewords = ($spec[2] * $spec[3]) + ($spec[4] * $spec[5]);

        $bits = '';
        // Byte mode indicator: 0100
        $bits .= '0100';

        // Character count indicator: 8 bits for versions 1-9, 16 bits for version 10
        $ccBits = ($version <= 9) ? 8 : 16;
        $bits .= sprintf("%0{$ccBits}b", $byteCount);

        // Data bytes
        foreach ($dataBytes as $b) {
            $bits .= sprintf('%08b', $b);
        }

        // Terminator: up to 4 zero bits
        $capBits = $totalDataCodewords * 8;
        $rem = $capBits - strlen($bits);
        if ($rem > 0) {
            $bits .= str_repeat('0', min(4, $rem));
        }

        // Pad to nearest byte
        while (strlen($bits) % 8 !== 0) {
            $bits .= '0';
        }

        // Pad bytes (0xEC, 0x11 alternating) until capacity reached
        $padBytes = ['11101100', '00010001'];
        $padIdx = 0;
        while (strlen($bits) < $capBits) {
            $bits .= $padBytes[$padIdx % 2];
            $padIdx++;
        }

        // Convert bits to byte array
        $codewords = [];
        for ($i = 0; $i < strlen($bits); $i += 8) {
            $codewords[] = bindec(substr($bits, $i, 8));
        }

        return $codewords;
    }

    /**
     * Divide data codewords into blocks, compute EC for each block, and interleave.
     */
    private static function structureFinalMessage(array $dataCodewords, int $version): array
    {
        $spec = self::$versionSpecsM[$version];
        $ecPerBlock = $spec[1];
        $blocks = [];

        $offset = 0;
        // Group 1
        for ($b = 0; $b < $spec[2]; $b++) {
            $len = $spec[3];
            $d = array_slice($dataCodewords, $offset, $len);
            $offset += $len;
            $blocks[] = [
                'data' => $d,
                'ec' => self::rsCalculate($d, $ecPerBlock)
            ];
        }
        // Group 2
        for ($b = 0; $b < $spec[4]; $b++) {
            $len = $spec[5];
            $d = array_slice($dataCodewords, $offset, $len);
            $offset += $len;
            $blocks[] = [
                'data' => $d,
                'ec' => self::rsCalculate($d, $ecPerBlock)
            ];
        }

        // Interleave data codewords
        $final = [];
        $maxDataLen = max($spec[3], $spec[5]);
        for ($i = 0; $i < $maxDataLen; $i++) {
            foreach ($blocks as &$blk) {
                if ($i < count($blk['data'])) {
                    $final[] = $blk['data'][$i];
                }
            }
        }

        // Interleave error correction codewords
        for ($i = 0; $i < $ecPerBlock; $i++) {
            foreach ($blocks as &$blk) {
                $final[] = $blk['ec'][$i];
            }
        }

        return $final;
    }

    /**
     * Create matrix and place function patterns.
     */
    private static function createMatrix(int $version): array
    {
        $size = 17 + (4 * $version);
        $matrix = array_fill(0, $size, array_fill(0, $size, null));
        $isFunction = array_fill(0, $size, array_fill(0, $size, false));

        // 1. Finder patterns (7x7) + Separators (8x8)
        $finders = [[0, 0], [0, $size - 7], [$size - 7, 0]];
        foreach ($finders as [$r, $c]) {
            for ($dr = -1; $dr <= 7; $dr++) {
                for ($dc = -1; $dc <= 7; $dc++) {
                    $row = $r + $dr;
                    $col = $c + $dc;
                    if ($row >= 0 && $row < $size && $col >= 0 && $col < $size) {
                        $isFunction[$row][$col] = true;
                        // Finder interior
                        if ($dr >= 0 && $dr <= 6 && $dc >= 0 && $dc <= 6) {
                            if ($dr === 0 || $dr === 6 || $dc === 0 || $dc === 6 ||
                                ($dr >= 2 && $dr <= 4 && $dc >= 2 && $dc <= 4)) {
                                $matrix[$row][$col] = 1;
                            } else {
                                $matrix[$row][$col] = 0;
                            }
                        } else {
                            $matrix[$row][$col] = 0; // Separator
                        }
                    }
                }
            }
        }

        // 2. Timing patterns
        for ($i = 8; $i < $size - 8; $i++) {
            if (!$isFunction[6][$i]) {
                $matrix[6][$i] = ($i % 2 === 0) ? 1 : 0;
                $isFunction[6][$i] = true;
            }
            if (!$isFunction[$i][6]) {
                $matrix[$i][6] = ($i % 2 === 0) ? 1 : 0;
                $isFunction[$i][6] = true;
            }
        }

        // 3. Dark module (always dark) at (4 * V + 9, 8)
        $dmRow = 4 * $version + 9;
        $matrix[$dmRow][8] = 1;
        $isFunction[$dmRow][8] = true;

        // 4. Alignment patterns
        $coords = self::$alignmentCoords[$version] ?? [];
        foreach ($coords as $ar) {
            foreach ($coords as $ac) {
                // Skip if overlapping finder patterns
                if ($isFunction[$ar][$ac]) {
                    continue;
                }
                for ($dr = -2; $dr <= 2; $dr++) {
                    for ($dc = -2; $dc <= 2; $dc++) {
                        $row = $ar + $dr;
                        $col = $ac + $dc;
                        $isFunction[$row][$col] = true;
                        if (abs($dr) === 2 || abs($dc) === 2 || ($dr === 0 && $dc === 0)) {
                            $matrix[$row][$col] = 1;
                        } else {
                            $matrix[$row][$col] = 0;
                        }
                    }
                }
            }
        }

        // 5. Reserve format info areas
        for ($i = 0; $i <= 8; $i++) {
            $isFunction[8][$i] = true;
            $isFunction[$i][8] = true;
        }
        for ($i = 0; $i <= 7; $i++) {
            $isFunction[8][$size - 1 - $i] = true;
            $isFunction[$size - 1 - $i][8] = true;
        }

        return [$matrix, $isFunction, $size];
    }

    /**
     * Standard QR mask condition.
     */
    private static function isMasked(int $mask, int $r, int $c): bool
    {
        return match ($mask) {
            0 => ($r + $c) % 2 === 0,
            1 => $r % 2 === 0,
            2 => $c % 3 === 0,
            3 => ($r + $c) % 3 === 0,
            4 => ((int)($r / 2) + (int)($c / 3)) % 2 === 0,
            5 => (($r * $c) % 2) + (($r * $c) % 3) === 0,
            6 => ((($r * $c) % 2) + (($r * $c) % 3)) % 2 === 0,
            7 => ((($r + $c) % 2) + (($r * $c) % 3)) % 2 === 0,
            default => false,
        };
    }

    /**
     * Format information strings (BCH(15,5) XOR 0x5412) for EC Level M (00 in 2-bit code)
     */
    private static array $formatInfoM = [
        0 => '101010000010010',
        1 => '101000100100101',
        2 => '101111001111100',
        3 => '101101101001011',
        4 => '100010111111001',
        5 => '100000011001110',
        6 => '100111110010111',
        7 => '100101010100000',
    ];

    /**
     * Generate pure SVG QR code from string.
     *
     * @param string $text Content/URL to encode
     * @param int $size Width and height in px
     * @param int $margin Quiet zone in modules (minimum 2 recommended for thermal labels)
     * @return string Vector SVG element
     */
    public static function generateSvg(string $text, int $size = 140, int $margin = 2): string
    {
        $text = trim($text);
        if ($text === '') {
            $text = 'EMPTY';
        }

        $version = self::selectVersion(strlen($text));
        $dataCodewords = self::encodeData($text, $version);
        $finalCodewords = self::structureFinalMessage($dataCodewords, $version);

        [$matrix, $isFunction, $dimension] = self::createMatrix($version);

        // Convert codewords to bitstring
        $dataBits = '';
        foreach ($finalCodewords as $cw) {
            $dataBits .= sprintf('%08b', $cw);
        }

        // Place data bits in zig-zag 2-column bands
        $bitIdx = 0;
        $numBits = strlen($dataBits);
        $up = true;

        for ($c = $dimension - 1; $c > 0; $c -= 2) {
            if ($c === 6) { // Skip vertical timing column
                $c--;
            }

            $rows = $up ? range($dimension - 1, 0, -1) : range(0, $dimension - 1);
            foreach ($rows as $r) {
                for ($colOffset = 0; $colOffset < 2; $colOffset++) {
                    $col = $c - $colOffset;
                    if (!$isFunction[$r][$col]) {
                        $bit = ($bitIdx < $numBits) ? (int)$dataBits[$bitIdx] : 0;
                        $matrix[$r][$col] = $bit;
                        $bitIdx++;
                    }
                }
            }
            $up = !$up;
        }

        // Apply standard mask pattern (Mask 0 is highly effective & standard for alphanumeric URLs)
        $mask = 0;
        for ($r = 0; $r < $dimension; $r++) {
            for ($c = 0; $c < $dimension; $c++) {
                if (!$isFunction[$r][$c]) {
                    if (self::isMasked($mask, $r, $c)) {
                        $matrix[$r][$c] ^= 1;
                    }
                }
            }
        }

        // Write Format Information bits
        $fmtBits = self::$formatInfoM[$mask];

        // Format info along top-left finder
        $pos = [
            [8, 0], [8, 1], [8, 2], [8, 3], [8, 4], [8, 5], [8, 7], [8, 8],
            [7, 8], [5, 8], [4, 8], [3, 8], [2, 8], [1, 8], [0, 8]
        ];
        for ($i = 0; $i < 15; $i++) {
            [$r, $c] = $pos[$i];
            $matrix[$r][$c] = (int)$fmtBits[$i];
        }

        // Format info along bottom-left and top-right finders
        for ($i = 0; $i < 7; $i++) {
            $matrix[$dimension - 1 - $i][8] = (int)$fmtBits[$i];
        }
        for ($i = 0; $i < 8; $i++) {
            $matrix[8][$dimension - 8 + $i] = (int)$fmtBits[7 + $i];
        }

        // Build compact SVG path
        $totalModules = $dimension + ($margin * 2);
        $pathD = '';

        for ($r = 0; $r < $dimension; $r++) {
            for ($c = 0; $c < $dimension; $c++) {
                if ($matrix[$r][$c] === 1) {
                    $x = $c + $margin;
                    $y = $r + $margin;
                    $pathD .= "M{$x},{$y}h1v1h-1z ";
                }
            }
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$totalModules} {$totalModules}" width="{$size}" height="{$size}" shape-rendering="crispEdges" style="display:block; margin:0 auto;">
    <rect width="100%" height="100%" fill="#ffffff" />
    <path d="{$pathD}" fill="#000000" />
</svg>
SVG;
    }

    /**
     * Build standard tracking payload for courier packages.
     */
    public static function generateTrackingPayload(array $c, string $baseUrl = ''): string
    {
        $awb = $c['consignment_no'] ?? '';
        if ($baseUrl === '') {
            $baseUrl = 'https://kesari.in';
        }
        $dest = $c['dest_pincode'] ?? $c['destination_name'] ?? '';
        $wt = $c['billable_weight'] ?? $c['dead_weight'] ?? '1';
        $pcs = $c['pieces'] ?? '1';

        // Returns URL formatted with quick verification query params for scanner apps
        return "{$baseUrl}/track?awb={$awb}&dst={$dest}&wt={$wt}&p={$pcs}";
    }
}
