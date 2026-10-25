// backend/src/utils/volumetric.ts

export interface WeightCalculationResult {
  deadWeight: number;
  lengthCm?: number;
  widthCm?: number;
  heightCm?: number;
  volumetricWeight: number | null;
  billableWeight: number;
  divisor: number;
}

/**
 * Calculates volumetric weight according to IATA standards:
 * Air Express Divisor: 5000 (cm^3 / kg)
 * Surface Cargo Divisor: 4000 (cm^3 / kg)
 */
export function calculateWeight(
  deadWeight: number,
  sendBy: string = 'Air',
  lengthCm?: number,
  widthCm?: number,
  heightCm?: number
): WeightCalculationResult {
  const divisor = sendBy === 'Air' ? 5000 : sendBy === 'Fast Surface' ? 4500 : 4000;
  let volumetricWeight: number | null = null;

  if (lengthCm && widthCm && heightCm && lengthCm > 0 && widthCm > 0 && heightCm > 0) {
    const rawVolume = (lengthCm * widthCm * heightCm) / divisor;
    volumetricWeight = Math.round(rawVolume * 100) / 100;
  }

  const effectiveDeadWeight = Math.round(deadWeight * 100) / 100;
  const billableWeight = volumetricWeight
    ? Math.max(effectiveDeadWeight, volumetricWeight)
    : effectiveDeadWeight;

  return {
    deadWeight: effectiveDeadWeight,
    lengthCm,
    widthCm,
    heightCm,
    volumetricWeight,
    billableWeight,
    divisor,
  };
}
