<?php
require_once 'functions.php';

// Call the reminder sender
sendTaskReminders();
#!/bin/bash

# Get the absolute path to cron.php
CRON_FILE="$(cd "$(dirname "$0")"; pwd)/cron.php"

# Create a cron job entry to run cron.php every hour
# You might need to change /usr/bin/php depending on your system's PHP path
CRON_CMD="0 * * * * /usr/bin/php $CRON_FILE > /dev/null 2>&1"

# Install the new cron job if it's not already present
(crontab -l 2>/dev/null | grep -Fv "$CRON_FILE"; echo "$CRON_CMD") | crontab -

echo "✅ CRON job installed to run cron.php every hour!"

