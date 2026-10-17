<?php
// src/Services/GstService.php - Indian GST Logistics Tax Calculation & Currency Words
// Computes fuel surcharges, SAC 996812 tax split (CGST+SGST vs IGST), and round-offs.

namespace App\Services;

class GstService
{
    /**
     * Compute comprehensive GST tax breakdown.
     */
    public static function calculate(
        float $subtotal,
        float $fuelSurchargePercent,
        float $docketCharges,
        string $agencyGstin,
        string $clientGstin,
        string $agencyState = 'Chhattisgarh',
        string $clientState = 'Chhattisgarh'
    ): array {
        $subtotal = round($subtotal, 2);
        $fuelPercent = max(0, $fuelSurchargePercent);
        $fuelAmount = round($subtotal * ($fuelPercent / 100), 2);
        $docketCharges = round(max(0, $docketCharges), 2);

        $taxableAmount = round($subtotal + $fuelAmount + $docketCharges, 2);

        // Determine GST Split: Intra-State (CGST+SGST) vs Inter-State (IGST)
        $isIntraState = self::isIntraState($agencyGstin, $clientGstin, $agencyState, $clientState);

        if ($isIntraState) {
            $cgstRate = 9.0;
            $sgstRate = 9.0;
            $igstRate = 0.0;
            $cgstAmount = round($taxableAmount * 0.09, 2);
            $sgstAmount = round($taxableAmount * 0.09, 2);
            $igstAmount = 0.00;
        } else {
            $cgstRate = 0.0;
            $sgstRate = 0.0;
            $igstRate = 18.0;
            $cgstAmount = 0.00;
            $sgstAmount = 0.00;
            $igstAmount = round($taxableAmount * 0.18, 2);
        }

        $totalGst = round($cgstAmount + $sgstAmount + $igstAmount, 2);
        $rawGrandTotal = $taxableAmount + $totalGst;
        $grandTotal = round($rawGrandTotal);
        $roundOff = round($grandTotal - $rawGrandTotal, 2);

        return [
            'subtotal' => $subtotal,
            'fuel_percent' => $fuelPercent,
            'fuel_amount' => $fuelAmount,
            'docket_charges' => $docketCharges,
            'taxable_amount' => $taxableAmount,
            'is_intra_state' => $isIntraState,
            'cgst_rate' => $cgstRate,
            'cgst_amount' => $cgstAmount,
            'sgst_rate' => $sgstRate,
            'sgst_amount' => $sgstAmount,
            'igst_rate' => $igstRate,
            'igst_amount' => $igstAmount,
            'total_gst' => $totalGst,
            'round_off' => $roundOff,
            'grand_total' => $grandTotal,
            'amount_in_words' => self::numberToWords((int)$grandTotal) . ' Only',
        ];
    }

    /**
     * Check if origin and destination are in the same Indian state.
     */
    private static function isIntraState(string $agencyGstin, string $clientGstin, string $agencyState, string $clientState): bool
    {
        $agencyGstin = trim($agencyGstin);
        $clientGstin = trim($clientGstin);

        // If both GSTINs are provided and valid (>= 2 chars), compare the first 2 state digits
        if (strlen($agencyGstin) >= 2 && strlen($clientGstin) >= 2 && ctype_digit(substr($agencyGstin, 0, 2)) && ctype_digit(substr($clientGstin, 0, 2))) {
            return substr($agencyGstin, 0, 2) === substr($clientGstin, 0, 2);
        }

        // Fallback to state name comparison
        return strcasecmp(trim($agencyState), trim($clientState)) === 0;
    }

    /**
     * Convert integer currency to Indian words (Crores, Lakhs, Thousands, Hundreds).
     */
    public static function numberToWords(int $number): string
    {
        if ($number === 0) {
            return 'Rupees Zero';
        }

        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
            5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
            20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
            60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        ];

        $output = '';

        if ($number >= 10000000) {
            $crores = (int)($number / 10000000);
            $output .= self::convertGroup($crores, $words) . ' Crore ';
            $number %= 10000000;
        }

        if ($number >= 100000) {
            $lakhs = (int)($number / 100000);
            $output .= self::convertGroup($lakhs, $words) . ' Lakh ';
            $number %= 100000;
        }

        if ($number >= 1000) {
            $thousands = (int)($number / 1000);
            $output .= self::convertGroup($thousands, $words) . ' Thousand ';
            $number %= 1000;
        }

        if ($number >= 100) {
            $hundreds = (int)($number / 100);
            $output .= self::convertGroup($hundreds, $words) . ' Hundred ';
            $number %= 100;
        }

        if ($number > 0) {
            $output .= self::convertGroup($number, $words);
        }

        return 'Rupees ' . trim($output);
    }

    private static function convertGroup(int $num, array $words): string
    {
        if ($num <= 20) {
            return $words[$num];
        }
        $tens = (int)($num / 10) * 10;
        $units = $num % 10;
        return trim($words[$tens] . ' ' . $words[$units]);
    }
}
