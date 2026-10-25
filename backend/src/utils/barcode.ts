// backend/src/utils/barcode.ts
// Pure Vector Code 128 Barcode SVG Generator

const patterns: { [key: number]: string } = {
  0: '212222', 1: '222122', 2: '222221', 3: '121223', 4: '121322',
  5: '131222', 6: '122213', 7: '122312', 8: '132212', 9: '221213',
  10: '221312', 11: '231212', 12: '112232', 13: '122132', 14: '122231',
  15: '113222', 16: '123122', 17: '123221', 18: '223211', 19: '221132',
  20: '221231', 21: '213212', 22: '223112', 23: '312131', 24: '311222',
  25: '321122', 26: '321221', 27: '312212', 28: '322112', 29: '322211',
  30: '212123', 31: '212321', 32: '232121', 33: '111323', 34: '131123',
  35: '131321', 36: '112313', 37: '132113', 38: '132311', 39: '211313',
  40: '231113', 41: '231311', 42: '112133', 43: '112331', 44: '132131',
  45: '113123', 46: '113321', 47: '133121', 48: '313121', 49: '211331',
  50: '231131', 51: '213113', 52: '213311', 53: '213131', 54: '311123',
  55: '311321', 56: '331121', 57: '312113', 58: '312311', 59: '332111',
  60: '314111', 61: '221411', 62: '431111', 63: '111224', 64: '111422',
  65: '121124', 66: '121421', 67: '141122', 68: '141221', 69: '112214',
  70: '112412', 71: '122114', 72: '122411', 73: '142112', 74: '142211',
  75: '241211', 76: '221114', 77: '413111', 78: '241112', 79: '134111',
  80: '111242', 81: '121142', 82: '121241', 83: '114212', 84: '124112',
  85: '124211', 86: '411212', 87: '421112', 88: '421211', 89: '212141',
  90: '214121', 91: '412121', 92: '111143', 93: '111341', 94: '131141',
  95: '114113', 96: '114311', 97: '411113', 98: '411311', 99: '113141',
  100: '114131', 101: '311141', 102: '411131', 103: '211412', 104: '211214',
  105: '211232', 106: '2331112'
};

export function generateBarcodeSvg(
  text: string,
  height: number = 60,
  moduleWidth: number = 2.0,
  showText: boolean = true
): string {
  const safeText = text.trim() || 'SAMPLE';
  const startCode = 104; // Code 128 Subset B
  let checksum = startCode;
  const charIndices: number[] = [startCode];

  for (let i = 0; i < safeText.length; i++) {
    const ascii = safeText.charCodeAt(i);
    let val = ascii - 32;
    if (val < 0 || val > 95) val = 0;
    charIndices.push(val);
    checksum += (i + 1) * val;
  }

  const checkVal = checksum % 103;
  charIndices.push(checkVal);
  charIndices.push(106); // Stop code

  let modules = '';
  for (const idx of charIndices) {
    const pattern = patterns[idx] || patterns[0];
    for (let p = 0; p < pattern.length; p++) {
      const width = parseInt(pattern[p], 10);
      const bit = p % 2 === 0 ? '1' : '0';
      modules += bit.repeat(width);
    }
  }

  const quietZone = 10;
  const totalWidth = (modules.length + quietZone * 2) * moduleWidth;
  const textHeight = showText ? 16 : 0;
  const totalHeight = height + textHeight;

  let rects = '';
  let currentX = quietZone * moduleWidth;
  for (let i = 0; i < modules.length; i++) {
    if (modules[i] === '1') {
      rects += `<rect x="${currentX.toFixed(1)}" y="0" width="${moduleWidth.toFixed(1)}" height="${height}" fill="#000000" />\n`;
    }
    currentX += moduleWidth;
  }

  let textElement = '';
  if (showText) {
    textElement = `<text x="${(totalWidth / 2).toFixed(1)}" y="${height + 14}" font-family="monospace, monospace" font-size="12" font-weight="bold" text-anchor="middle" fill="#000000" letter-spacing="2">${safeText}</text>`;
  }

  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${Math.ceil(totalWidth)} ${totalHeight}" width="${Math.ceil(totalWidth)}" height="${totalHeight}">
    <rect width="100%" height="100%" fill="#ffffff"/>
    ${rects}
    ${textElement}
  </svg>`;
}
