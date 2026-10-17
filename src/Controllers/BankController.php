<?php
// src/Controllers/BankController.php - Agency Bank Accounts Management
// Handles registration and primary selection of bank accounts for GST Tax Invoices

class BankController
{
    public function index(): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $accounts = $db->fetchAll("
            SELECT * FROM agency_bank_accounts 
            WHERE tenant_id = ? 
            ORDER BY is_primary DESC, id DESC
        ", [$tenantId]);

        require ROOT_PATH . '/src/Views/bank/index.php';
    }

    public function store(): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $bankName = trim($_POST['bank_name'] ?? '');
        $accountNo = trim($_POST['account_no'] ?? '');
        $holderName = trim($_POST['account_holder_name'] ?? '');
        $ifsc = strtoupper(trim($_POST['ifsc_code'] ?? ''));
        $branch = trim($_POST['branch_name'] ?? '');
        $isPrimary = !empty($_POST['is_primary']) ? 1 : 0;

        if ($bankName === '' || $accountNo === '' || $holderName === '' || $ifsc === '' || $branch === '') {
            $_SESSION['flash_error'] = "All bank details (Bank Name, Account Number, Beneficiary Name, IFSC, Branch) are required.";
            header('Location: /bank-accounts');
            exit;
        }

        // If marked primary or if this is the first account, set primary
        $existingCount = (int)$db->fetchOne("SELECT COUNT(*) as c FROM agency_bank_accounts WHERE tenant_id = ?", [$tenantId])['c'];
        if ($existingCount === 0) {
            $isPrimary = 1;
        }

        if ($isPrimary) {
            $db->execute("UPDATE agency_bank_accounts SET is_primary = 0 WHERE tenant_id = ?", [$tenantId]);
        }

        $db->execute("
            INSERT INTO agency_bank_accounts (tenant_id, bank_name, account_no, account_holder_name, ifsc_code, branch_name, is_primary)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ", [$tenantId, $bankName, $accountNo, $holderName, $ifsc, $branch, $isPrimary]);

        $_SESSION['flash_success'] = "✓ Bank account for {$bankName} registered successfully.";
        header('Location: /bank-accounts');
        exit;
    }

    public function makePrimary(int $id): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $account = $db->fetchOne("SELECT * FROM agency_bank_accounts WHERE id = ? AND tenant_id = ?", [$id, $tenantId]);
        if ($account) {
            $db->execute("UPDATE agency_bank_accounts SET is_primary = 0 WHERE tenant_id = ?", [$tenantId]);
            $db->execute("UPDATE agency_bank_accounts SET is_primary = 1 WHERE id = ?", [$id]);
            $_SESSION['flash_success'] = "✓ {$account['bank_name']} (A/C: {$account['account_no']}) marked as primary billing account.";
        }

        header('Location: /bank-accounts');
        exit;
    }
}
