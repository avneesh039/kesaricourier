// frontend/src/components/icons/CustomLogisticsIcons.tsx
import React from 'react';

interface IconProps {
  size?: number;
  className?: string;
  color?: string;
}

// 1. Custom Numpad Speed Icon
export const IconNumpadSpeed: React.FC<IconProps> = ({ size = 28, className = '' }) => (
  <svg width={size} height={size} viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
    <defs>
      <linearGradient id="gradNumpad" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
        <stop stopColor="#3b82f6" />
        <stop offset="1" stopColor="#1d4ed8" />
      </linearGradient>
      <linearGradient id="gradGoldKey" x1="0" y1="0" x2="10" y2="10" gradientUnits="userSpaceOnUse">
        <stop stopColor="#fbbf24" />
        <stop offset="1" stopColor="#d97706" />
      </linearGradient>
    </defs>
    <rect x="3" y="2" width="26" height="28" rx="6" fill="url(#gradNumpad)" />
    <rect x="6" y="5" width="20" height="4" rx="2" fill="#ffffff" fillOpacity="0.25" />
    {/* Key Grid */}
    <rect x="6" y="11" width="5" height="4" rx="1.5" fill="#ffffff" fillOpacity="0.9" />
    <rect x="13.5" y="11" width="5" height="4" rx="1.5" fill="#ffffff" fillOpacity="0.9" />
    <rect x="21" y="11" width="5" height="4" rx="1.5" fill="#ffffff" fillOpacity="0.9" />
    <rect x="6" y="17" width="5" height="4" rx="1.5" fill="#ffffff" fillOpacity="0.9" />
    <rect x="13.5" y="17" width="5" height="4" rx="1.5" fill="#ffffff" fillOpacity="0.9" />
    <rect x="21" y="17" width="5" height="4" rx="1.5" fill="#ffffff" fillOpacity="0.9" />
    <rect x="6" y="23" width="12.5" height="4" rx="1.5" fill="#ffffff" fillOpacity="0.9" />
    {/* Glowing Enter Key */}
    <rect x="21" y="23" width="5" height="4" rx="1.5" fill="url(#gradGoldKey)" />
    <path d="M24 24.5V25.5H22.5M22.5 25.5L23.2 24.8M22.5 25.5L23.2 26.2" stroke="#ffffff" strokeWidth="0.8" strokeLinecap="round" strokeLinejoin="round" />
  </svg>
);

// 2. Custom Sticky Lock Icon
export const IconStickyLock: React.FC<IconProps> = ({ size = 28, className = '' }) => (
  <svg width={size} height={size} viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
    <defs>
      <linearGradient id="gradLock" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
        <stop stopColor="#f59e0b" />
        <stop offset="1" stopColor="#b45309" />
      </linearGradient>
      <linearGradient id="gradShackle" x1="0" y1="0" x2="0" y2="16" gradientUnits="userSpaceOnUse">
        <stop stopColor="#fef3c7" />
        <stop offset="1" stopColor="#fcd34d" />
      </linearGradient>
    </defs>
    {/* Shackle with Barcode notches */}
    <path d="M10 13V9C10 5.68629 12.6863 3 16 3C19.3137 3 22 5.68629 22 9V13" stroke="url(#gradShackle)" strokeWidth="3.2" strokeLinecap="round" />
    <line x1="12" y1="6" x2="12" y2="9" stroke="#b45309" strokeWidth="1" />
    <line x1="16" y1="5" x2="16" y2="8" stroke="#b45309" strokeWidth="1" />
    <line x1="20" y1="6" x2="20" y2="9" stroke="#b45309" strokeWidth="1" />
    {/* Lock Body */}
    <rect x="5" y="13" width="22" height="16" rx="4" fill="url(#gradLock)" />
    {/* Keyhole Parcel */}
    <rect x="13.5" y="18" width="5" height="5" rx="1" fill="#ffffff" />
    <line x1="16" y1="18" x2="16" y2="23" stroke="#b45309" strokeWidth="0.8" />
    <circle cx="16" cy="19.5" r="0.8" fill="#b45309" />
  </svg>
);

// 3. Custom Volumetric Box Icon
export const IconVolumetricBox: React.FC<IconProps> = ({ size = 28, className = '' }) => (
  <svg width={size} height={size} viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
    <defs>
      <linearGradient id="gradBoxTop" x1="0" y1="0" x2="32" y2="0" gradientUnits="userSpaceOnUse">
        <stop stopColor="#60a5fa" />
        <stop offset="1" stopColor="#3b82f6" />
      </linearGradient>
      <linearGradient id="gradBoxLeft" x1="0" y1="0" x2="0" y2="32" gradientUnits="userSpaceOnUse">
        <stop stopColor="#2563eb" />
        <stop offset="1" stopColor="#1d4ed8" />
      </linearGradient>
      <linearGradient id="gradBoxRight" x1="0" y1="0" x2="0" y2="32" gradientUnits="userSpaceOnUse">
        <stop stopColor="#1e40af" />
        <stop offset="1" stopColor="#172554" />
      </linearGradient>
    </defs>
    {/* Isometric 3D Box */}
    <path d="M16 3L28 9.5L16 16L4 9.5L16 3Z" fill="url(#gradBoxTop)" />
    <path d="M4 9.5L16 16V29L4 22.5V9.5Z" fill="url(#gradBoxLeft)" />
    <path d="M28 9.5L16 16V29L28 22.5V9.5Z" fill="url(#gradBoxRight)" />
    {/* Tape */}
    <path d="M13 14.5L19 11L16 9.5L10 13L13 14.5Z" fill="#fbbf24" fillOpacity="0.85" />
    {/* Dimension Guides */}
    <path d="M29 23V10M30 10L28 10M30 23L28 23" stroke="#38bdf8" strokeWidth="1.2" strokeLinecap="round" />
    <path d="M15 31L3 24.5M2 24.5L4 25.5M14 31.5L16 30.5" stroke="#38bdf8" strokeWidth="1.2" strokeLinecap="round" />
  </svg>
);

// 4. Custom Tally Ledger Icon
export const IconTallyLedger: React.FC<IconProps> = ({ size = 28, className = '' }) => (
  <svg width={size} height={size} viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
    <defs>
      <linearGradient id="gradBook" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
        <stop stopColor="#059669" />
        <stop offset="1" stopColor="#065f46" />
      </linearGradient>
      <linearGradient id="gradRupee" x1="0" y1="0" x2="16" y2="16" gradientUnits="userSpaceOnUse">
        <stop stopColor="#fef08a" />
        <stop offset="1" stopColor="#f59e0b" />
      </linearGradient>
    </defs>
    {/* Book Cover */}
    <rect x="4" y="3" width="24" height="26" rx="3" fill="url(#gradBook)" />
    {/* Book Pages */}
    <path d="M6 5H26V27H6C4.89543 27 4 26.1046 4 25V7C4 5.89543 4.89543 5 6 5Z" fill="#ffffff" fillOpacity="0.95" />
    {/* Golden Bookmark */}
    <path d="M20 3V11L22.5 9L25 11V3H20Z" fill="#f59e0b" />
    {/* Rupee Symbol */}
    <circle cx="15" cy="16" r="6" fill="url(#gradRupee)" />
    <path d="M12.5 13.5H17.5M12.5 15H17.5M12.5 13.5H14C15.5 13.5 16.5 14 16.5 15.5C16.5 16.8 15.5 17.2 14 17.2H12.5L17 21" stroke="#78350f" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" />
  </svg>
);

// 5. Custom GST SAC 996812 Invoice Icon
export const IconGstInvoice: React.FC<IconProps> = ({ size = 28, className = '' }) => (
  <svg width={size} height={size} viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
    <defs>
      <linearGradient id="gradDoc" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
        <stop stopColor="#ef4444" />
        <stop offset="1" stopColor="#991b1b" />
      </linearGradient>
    </defs>
    {/* Document Paper */}
    <path d="M6 3H20L27 10V27C27 28.1046 26.1046 29 25 29H6C4.89543 29 4 28.1046 4 27V5C4 3.89543 4.89543 3 6 3Z" fill="#ffffff" stroke="#e2e8f0" strokeWidth="1" />
    <path d="M20 3V10H27" fill="#cbd5e1" />
    {/* Header Banner */}
    <rect x="6" y="7" width="12" height="3" rx="1" fill="url(#gradDoc)" />
    {/* Document Lines */}
    <rect x="7" y="13" width="18" height="1.5" rx="0.75" fill="#94a3b8" />
    <rect x="7" y="16.5" width="14" height="1.5" rx="0.75" fill="#cbd5e1" />
    {/* Barcode Lines */}
    <rect x="7" y="21" width="1" height="5" fill="#0f172a" />
    <rect x="9" y="21" width="1.5" height="5" fill="#0f172a" />
    <rect x="11.5" y="21" width="0.8" height="5" fill="#0f172a" />
    <rect x="13" y="21" width="2" height="5" fill="#0f172a" />
    <rect x="16" y="21" width="1" height="5" fill="#0f172a" />
    {/* Green Verified Stamp */}
    <circle cx="22" cy="22" r="5" fill="#10b981" />
    <path d="M20 22L21.5 23.5L24.5 20.5" stroke="#ffffff" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" />
  </svg>
);

// 6. Custom Thermal Printer Icon
export const IconThermalPrinter: React.FC<IconProps> = ({ size = 28, className = '' }) => (
  <svg width={size} height={size} viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
    <defs>
      <linearGradient id="gradPrinter" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
        <stop stopColor="#1e293b" />
        <stop offset="1" stopColor="#0f172a" />
      </linearGradient>
    </defs>
    {/* Printer Body */}
    <rect x="4" y="8" width="24" height="18" rx="4" fill="url(#gradPrinter)" />
    {/* Blue Status Light Strip */}
    <rect x="7" y="11" width="18" height="2" rx="1" fill="#38bdf8" />
    {/* Laser Output Slot */}
    <rect x="7" y="16" width="18" height="3" rx="1.5" fill="#0284c7" />
    {/* 4x6" Paper Rolling Out */}
    <path d="M8 18H24V28C24 29 23 29.5 22 29.5H10C9 29.5 8 29 8 28V18Z" fill="#ffffff" stroke="#e2e8f0" strokeWidth="0.8" />
    {/* Barcode on Paper */}
    <line x1="10" y1="21" x2="10" y2="25" stroke="#000000" strokeWidth="1" />
    <line x1="12" y1="21" x2="12" y2="25" stroke="#000000" strokeWidth="1.5" />
    <line x1="14.5" y1="21" x2="14.5" y2="25" stroke="#000000" strokeWidth="0.8" />
    <line x1="16.5" y1="21" x2="16.5" y2="25" stroke="#000000" strokeWidth="1.8" />
    <line x1="19.5" y1="21" x2="19.5" y2="25" stroke="#000000" strokeWidth="1" />
    <line x1="21.5" y1="21" x2="21.5" y2="25" stroke="#000000" strokeWidth="0.8" />
  </svg>
);
