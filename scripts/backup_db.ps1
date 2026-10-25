# scripts/backup_db.ps1 - Windows PowerShell Automated MySQL Backup Script
$BackupDir = "C:\backups\kesari_mysql"
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$DbName = "kesari_saas"
$Mysqldump = "C:\xampp\mysql\bin\mysqldump.exe"

if (!(Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
}

$BackupFile = "$BackupDir\${DbName}_backup_$Timestamp.sql"
Write-Host "Backing up $DbName to $BackupFile..."

& $Mysqldump -u root $DbName --result-file=$BackupFile

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Database backup created successfully: $BackupFile" -ForegroundColor Green
} else {
    Write-Host "❌ Database backup failed!" -ForegroundColor Red
}
