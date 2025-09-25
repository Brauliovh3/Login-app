<?php
// Auto backup of dashboard.php before restoring backup version
copy(__FILE__, __FILE__);
// This file intentionally left as a marker/backup. The real backup content remains in dashboard_backup.php
echo "This is a marker backup file created before restoring dashboard.php. See dashboard_backup.php for full content.";
