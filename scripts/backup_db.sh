#!/bin/bash
# scripts/backup_db.sh - Automated Daily MySQL Database Backup Script
# Schedule with cron: 0 2 * * * /var/www/kesaricourier/scripts/backup_db.sh

BACKUP_DIR="/var/backups/kesari_mysql"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_NAME="kesari_saas"
DB_USER="root"
DB_PASS=""
RETENTION_DAYS=30

mkdir -p "$BACKUP_DIR"

BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_backup_$TIMESTAMP.sql.gz"

echo "Starting automated database backup for $DB_NAME at $(date)..."

# Dump and compress
mysqldump -u "$DB_USER" -p"$DB_PASS" --single-transaction --quick "$DB_NAME" | gzip > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "✓ Backup successfully created: $BACKUP_FILE"
else
    echo "❌ Backup failed!" >&2
    exit 1
fi

# Rotate backups older than retention period
find "$BACKUP_DIR" -type f -name "${DB_NAME}_backup_*.sql.gz" -mtime +$RETENTION_DAYS -exec rm -f {} \;
echo "✓ Old backups older than $RETENTION_DAYS days rotated."
