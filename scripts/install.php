<?php
/**
 * SuperMarket Installer
 * Run from CLI: php /var/www/html/supermarket/scripts/install.php
 * Or via browser: https://dev.ecoco.app/supermarket/scripts/install.php
 */

$isCli = php_sapi_name() === 'cli';

function output($msg, $type = 'info') {
    global $isCli;
    if ($isCli) {
        $prefixes = ['info' => '  ', 'success' => '✓ ', 'error' => '✗ ', 'header' => "\n== "];
        echo ($prefixes[$type] ?? '  ') . $msg . "\n";
    } else {
        $colors = ['info' => '#333', 'success' => '#28a745', 'error' => '#dc3545', 'header' => '#007bff'];
        $weight = $type === 'header' ? 'bold' : 'normal';
        echo "<div style='color:{$colors[$type]};font-weight:{$weight};margin:4px 0;font-family:monospace'>{$msg}</div>";
    }
}

if (!$isCli) {
    echo '<!DOCTYPE html><html><head><title>SuperMarket Installer</title></head><body style="padding:30px;max-width:800px;margin:auto">';
    echo '<h2>SuperMarket Installation</h2><hr>';
}

// Load database config
require_once __DIR__ . '/../config/database.php';

output('SuperMarket Installation', 'header');

// Step 1: Create database
output('Creating database...', 'header');
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    output('Database "' . DB_NAME . '" created/verified', 'success');

    $pdo->exec("USE `" . DB_NAME . "`");
} catch (PDOException $e) {
    output('Database connection failed: ' . $e->getMessage(), 'error');
    exit(1);
}

// Step 2: Run schema
output('Running schema migration...', 'header');
$schemaFile = __DIR__ . '/../migrations/001_create_schema.sql';
if (file_exists($schemaFile)) {
    $sql = file_get_contents($schemaFile);
    // Split by semicolons, handling multi-line statements
    $statements = array_filter(array_map('trim', preg_split('/;\s*$/m', $sql)));
    $count = 0;
    foreach ($statements as $stmt) {
        if (empty($stmt)) continue;
        try {
            $pdo->exec($stmt);
            $count++;
        } catch (PDOException $e) {
            // Ignore "table already exists" errors
            if (strpos($e->getMessage(), 'already exists') === false) {
                output('SQL Error: ' . $e->getMessage(), 'error');
                output('Statement: ' . substr($stmt, 0, 100) . '...', 'info');
            }
        }
    }
    output("Executed {$count} SQL statements", 'success');
} else {
    output('Schema file not found: ' . $schemaFile, 'error');
}

// Step 3: Run seed data
output('Running seed data...', 'header');
$seedFile = __DIR__ . '/../migrations/002_seed_data.sql';
if (file_exists($seedFile)) {
    $sql = file_get_contents($seedFile);
    $statements = array_filter(array_map('trim', preg_split('/;\s*$/m', $sql)));
    $count = 0;
    foreach ($statements as $stmt) {
        if (empty($stmt)) continue;
        try {
            $pdo->exec($stmt);
            $count++;
        } catch (PDOException $e) {
            // Ignore duplicate entry errors for seed data
            if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                output('Seed Error: ' . $e->getMessage(), 'error');
            }
        }
    }
    output("Executed {$count} seed statements", 'success');
} else {
    output('Seed file not found: ' . $seedFile, 'error');
}

// Step 4: Create upload directories
output('Creating directories...', 'header');
$dirs = [
    __DIR__ . '/../public/uploads/products',
    __DIR__ . '/../public/uploads/users',
    __DIR__ . '/../cache'
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
        output('Created: ' . str_replace(__DIR__ . '/..', '', $dir), 'success');
    } else {
        output('Exists: ' . str_replace(__DIR__ . '/..', '', $dir), 'info');
    }
}

// Step 5: Check PHP extensions
output('Checking PHP extensions...', 'header');
$required = ['pdo', 'pdo_mysql', 'gd', 'json', 'mbstring', 'openssl'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        output("Extension {$ext}: OK", 'success');
    } else {
        output("Extension {$ext}: MISSING", 'error');
    }
}

// Step 6: Verify admin user
output('Verifying admin user...', 'header');
$stmt = $pdo->query("SELECT id, email, role FROM users WHERE role = 'admin' LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    output("Admin user found: {$admin['email']}", 'success');
} else {
    output('No admin user found! Creating default...', 'info');
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role, is_active) VALUES (?, ?, ?, ?, ?, ?)")
        ->execute(['Admin', 'User', 'admin@supermarket.com', $hash, 'admin', 1]);
    output('Created admin: admin@supermarket.com / admin123', 'success');
}

// Summary
output('Installation Complete!', 'header');
output('URL: ' . (defined('APP_URL') ? APP_URL : 'https://dev.ecoco.app/supermarket'), 'info');
output('Admin Login: admin@supermarket.com / admin123', 'info');
output('IMPORTANT: Change the admin password after first login!', 'info');
output('IMPORTANT: Update Stripe keys in config/stripe.php', 'info');

if (!$isCli) {
    echo '<hr><p><a href="../" style="color:#28a745;font-size:18px">Go to SuperMarket →</a></p></body></html>';
}
