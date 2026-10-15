<?php
// src/Services/KycService.php - KYC Document Upload & Compliance Service

require_once dirname(__DIR__) . '/Database.php';

class KycService {
    public static function storeDocument(int $tenantId, string $docType, array $file): array {
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload failed or no file provided.'];
        }

        $allowedExts = ['pdf', 'png', 'jpg', 'jpeg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts, true)) {
            return ['success' => false, 'error' => 'Invalid file format. Allowed: PDF, PNG, JPG.'];
        }

        if ($file['size'] > 3 * 1024 * 1024) { // 3MB limit
            return ['success' => false, 'error' => 'File size exceeds 3MB limit.'];
        }

        $uploadDir = ROOT_PATH . '/storage/kyc';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $safeName = "tenant_{$tenantId}_{$docType}_" . bin2hex(random_bytes(6)) . ".{$ext}";
        $destPath = $uploadDir . '/' . $safeName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['success' => true, 'path' => '/storage/kyc/' . $safeName];
        }

        return ['success' => false, 'error' => 'Could not save file to disk.'];
    }

    public static function submitKyc(int $tenantId, array $data, array $files): array {
        $db = Database::getInstance();

        $panNo = strtoupper(trim($data['pan_no'] ?? ''));
        $aadhaarNo = preg_replace('/[^0-9]/', '', $data['aadhaar_no'] ?? '');
        $gstin = strtoupper(trim($data['gstin'] ?? ''));
        $address = trim($data['address'] ?? '');
        $pincode = trim($data['pincode'] ?? '');

        if (empty($panNo) || strlen($panNo) !== 10) {
            return ['success' => false, 'error' => 'Valid 10-character PAN number is mandatory.'];
        }

        if (empty($aadhaarNo) || strlen($aadhaarNo) !== 12) {
            return ['success' => false, 'error' => 'Valid 12-digit Aadhaar number is mandatory.'];
        }

        // Upload PAN document
        $panUpload = self::storeDocument($tenantId, 'pan', $files['pan_doc'] ?? []);
        if (!$panUpload['success']) {
            return ['success' => false, 'error' => 'PAN Card Document: ' . $panUpload['error']];
        }

        // Upload Aadhaar document
        $aadhaarUpload = self::storeDocument($tenantId, 'aadhaar', $files['aadhaar_doc'] ?? []);
        if (!$aadhaarUpload['success']) {
            return ['success' => false, 'error' => 'Aadhaar Card Document: ' . $aadhaarUpload['error']];
        }

        // Update Tenant Record
        $db->execute("
            UPDATE tenants SET 
                pan_no = ?, 
                aadhaar_no = ?, 
                gstin = ?, 
                address = ?, 
                pincode = ?, 
                pan_doc_url = ?, 
                aadhaar_doc_url = ?, 
                status = 'pending_kyc'
            WHERE id = ?
        ", [
            $panNo,
            $aadhaarNo,
            $gstin ?: null,
            $address,
            $pincode,
            $panUpload['path'],
            $aadhaarUpload['path'],
            $tenantId
        ]);

        return ['success' => true];
    }
}
