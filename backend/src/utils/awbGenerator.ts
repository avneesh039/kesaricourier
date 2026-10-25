// backend/src/utils/awbGenerator.ts
import prisma from '../config/database';

export async function generateAwbNumber(agencyCode: string = '26', branchCode: string = '01'): Promise<string> {
  const now = new Date();
  const year = String(now.getFullYear()).substring(2); // e.g. "26"
  const month = String(now.getMonth() + 1).padStart(2, '0'); // e.g. "10"
  const safeAgency = agencyCode.replace(/\D/g, '').padStart(2, '0').slice(-2);
  const safeBranch = branchCode.replace(/\D/g, '').padStart(2, '0').slice(-2);

  const prefix = `${year}${month}${safeAgency}${safeBranch}`;

  // Find latest consignment with this prefix
  const latest = await prisma.consignment.findFirst({
    where: {
      consignment_no: {
        startsWith: prefix,
      },
    },
    orderBy: {
      consignment_no: 'desc',
    },
    select: {
      consignment_no: true,
    },
  });

  let seq = 1;
  if (latest && latest.consignment_no.length >= 14) {
    const lastSeqStr = latest.consignment_no.substring(prefix.length);
    const parsed = parseInt(lastSeqStr, 10);
    if (!isNaN(parsed)) {
      seq = parsed + 1;
    }
  }

  const seqStr = String(seq).padStart(6, '0');
  return `${prefix}${seqStr}`;
}
