<?php

// Automatically pull from environment variables injected by Docker
$instance_name = getenv('APP_NAME') ?: 'Unknown Instance';
$subdomain     = getenv('SUBDOMAIN') ?: 'unknown.yourdomain.com';
$email_from    = getenv('EMAIL_FROM') ?: 'no-reply@unknown.example.com';

$db_host       = getenv('DB_HOST') ?: 'unknown';
$db_name       = getenv('DB_DATABASE') ?: 'unknown_db';
$db_user       = getenv('DB_USER') ?: 'unknown_user';
$db_pass       = getenv('DB_PASSWORD') ?: 'unknown';

// Test database connection
$dsn = "pgsql:host=$db_host;dbname=$db_name";
$connected = false;
$error = '';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $connected = true;
} catch (PDOException $e) {
    $error = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($instance_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f4; }
        .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 700px; margin: 0 auto; }
        h1 { color: #2c3e50; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Hello from <?php echo htmlspecialchars($instance_name); ?>! 🚀</h1>

        <p>This page is running correctly on: <strong><?php echo htmlspecialchars($subdomain); ?></strong></p>

        <table>
            <tr><th>Setting</th><th>Value</th></tr>
            <tr><td>Instance Name</td><td><?php echo htmlspecialchars($instance_name); ?></td></tr>
            <tr><td>Subdomain</td><td><?php echo htmlspecialchars($subdomain); ?></td></tr>
            <tr><td>Email From</td><td><?php echo htmlspecialchars($email_from); ?></td></tr>
            <tr><td>DB Host</td><td><?php echo htmlspecialchars($db_host); ?></td></tr>
            <tr><td>DB Name</td><td><?php echo htmlspecialchars($db_name); ?></td></tr>
            <tr><td>DB Connection</td>
                <td>
                    <?php if ($connected): ?>
                        <span class="success">✓ Connected successfully!</span>
                    <?php else: ?>
                        <span class="error">✗ Failed: <?php echo htmlspecialchars($error); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <hr>
        <p><strong>Everything is working!</strong> This single <code>index.php</code> is shared across all 5 instances.<br>
        When you're ready, replace or extend this file with your real API routes and logic.</p>
    </div>
</body>
</html>
