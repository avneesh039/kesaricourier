// backend/src/utils/gstCalc.ts
// Indian GST Logistics Tax Calculation & Currency Words Converter (SAC 996812)

export interface GstCalculationResult {
  subtotal: number;
  fuelPercent: number;
  fuelAmount: number;
  docketCharges: number;
  taxableAmount: number;
  isIntraState: boolean;
  cgstRate: number;
  cgstAmount: number;
  sgstRate: number;
  sgstAmount: number;
  igstRate: number;
  igstAmount: number;
  totalGst: number;
  roundOff: number;
  grandTotal: number;
  amountInWords: string;
}

export function calculateGst(
  subtotal: number,
  fuelSurchargePercent: number = 0,
  docketCharges: number = 0,
  agencyGstin: string = '',
  clientGstin: string = '',
  agencyState: string = 'Chhattisgarh',
  clientState: string = 'Chhattisgarh'
): GstCalculationResult {
  const cleanSubtotal = Math.round(subtotal * 100) / 100;
  const cleanFuelPercent = Math.max(0, fuelSurchargePercent);
  const fuelAmount = Math.round(cleanSubtotal * (cleanFuelPercent / 100) * 100) / 100;
  const cleanDocket = Math.round(Math.max(0, docketCharges) * 100) / 100;

  const taxableAmount = Math.round((cleanSubtotal + fuelAmount + cleanDocket) * 100) / 100;

  const isIntraState = checkIsIntraState(agencyGstin, clientGstin, agencyState, clientState);

  let cgstRate = 0;
  let sgstRate = 0;
  let igstRate = 0;
  let cgstAmount = 0;
  let sgstAmount = 0;
  let igstAmount = 0;

  if (isIntraState) {
    cgstRate = 9.0;
    sgstRate = 9.0;
    cgstAmount = Math.round(taxableAmount * 0.09 * 100) / 100;
    sgstAmount = Math.round(taxableAmount * 0.09 * 100) / 100;
  } else {
    igstRate = 18.0;
    igstAmount = Math.round(taxableAmount * 0.18 * 100) / 100;
  }

  const totalGst = Math.round((cgstAmount + sgstAmount + igstAmount) * 100) / 100;
  const rawGrandTotal = taxableAmount + totalGst;
  const grandTotal = Math.round(rawGrandTotal);
  const roundOff = Math.round((grandTotal - rawGrandTotal) * 100) / 100;

  return {
    subtotal: cleanSubtotal,
    fuelPercent: cleanFuelPercent,
    fuelAmount,
    docketCharges: cleanDocket,
    taxableAmount,
    isIntraState,
    cgstRate,
    cgstAmount,
    sgstRate,
    sgstAmount,
    igstRate,
    igstAmount,
    totalGst,
    roundOff,
    grandTotal,
    amountInWords: numberToWordsIndian(grandTotal) + ' Only',
  };
}

export function checkIsIntraState(
  agencyGstin: string,
  clientGstin: string,
  agencyState: string,
  clientState: string
): boolean {
  const cleanAgencyGst = agencyGstin.trim();
  const cleanClientGst = clientGstin.trim();

  // If both GSTINs are provided and have at least 2 digits, compare their first 2 state digits
  if (
    cleanAgencyGst.length >= 2 &&
    cleanClientGst.length >= 2 &&
    /^\d{2}/.test(cleanAgencyGst) &&
    /^\d{2}/.test(cleanClientGst)
  ) {
    return cleanAgencyGst.substring(0, 2) === cleanClientGst.substring(0, 2);
  }

  // Fallback to state names
  return agencyState.trim().toLowerCase() === clientState.trim().toLowerCase();
}

export function numberToWordsIndian(num: number): string {
  if (num === 0) return 'Rupees Zero';

  const a = [
    '',
    'One',
    'Two',
    'Three',
    'Four',
    'Five',
    'Six',
    'Seven',
    'Eight',
    'Nine',
    'Ten',
    'Eleven',
    'Twelve',
    'Thirteen',
    'Fourteen',
    'Fifteen',
    'Sixteen',
    'Seventeen',
    'Eighteen',
    'Nineteen',
  ];
  const b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

  function convertTwoDigits(n: number): string {
    if (n === 0) return '';
    if (n < 20) return a[n] + ' ';
    return b[Math.floor(n / 10)] + ' ' + (n % 10 !== 0 ? a[n % 10] + ' ' : '');
  }

  let words = '';
  const crore = Math.floor(num / 10000000);
  let remainder = num % 10000000;

  if (crore > 0) {
    words += convertTwoDigits(crore) + 'Crore ';
  }

  const lakh = Math.floor(remainder / 100000);
  remainder = remainder % 100000;
  if (lakh > 0) {
    words += convertTwoDigits(lakh) + 'Lakh ';
  }

  const thousand = Math.floor(remainder / 1000);
  remainder = remainder % 1000;
  if (thousand > 0) {
    words += convertTwoDigits(thousand) + 'Thousand ';
  }

  const hundred = Math.floor(remainder / 100);
  remainder = remainder % 100;
  if (hundred > 0) {
    words += a[hundred] + ' Hundred ';
  }

  if (remainder > 0) {
    words += convertTwoDigits(remainder);
  }

  return 'Rupees ' + words.trim();
}
