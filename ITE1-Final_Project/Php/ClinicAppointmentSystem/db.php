<?php
$host = 'localhost';
$dbname = 'test';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

function django_make_password($password, $iterations = 260000) {
    // Keep salt short-ish (Django uses printable ascii salts)
    $salt = substr(bin2hex(random_bytes(8)), 0, 16);
    $raw = hash_pbkdf2('sha256', $password, $salt, $iterations, 32, true);
    $b64 = base64_encode($raw);
    return sprintf('pbkdf2_sha256$%d$%s$%s', $iterations, $salt, $b64);
}

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    echo "Connected to DB `{$dbname}` on {$host}.<br>";

    // 1) Show whether auth_user exists and list columns
    try {
        $res = $pdo->query("SHOW TABLES LIKE 'auth_user'")->fetchAll();
        if (count($res) === 0) {
            echo "<b>ERROR:</b> Table <code>auth_user</code> not found in this database.<br>";
            echo "Make sure Django migrations have been run and PHP is pointing to the same database.<br>";
            exit;
        }
        echo "<b>Table auth_user found.</b><br>";

        $cols = $pdo->query("SHOW COLUMNS FROM auth_user")->fetchAll();
        echo "<pre>Columns in auth_user:\n";
        foreach ($cols as $c) {
            echo "{$c['Field']}    {$c['Type']}    Null:{$c['Null']}    Key:{$c['Key']}    Default:{$c['Default']}\n";
        }
        echo "</pre>";
    } catch (PDOException $e) {
        echo "<b>Could not show auth_user columns:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
        exit;
    }

    // 2) Create user — include common required fields used by Django
    $username = 'admin';
    $defaultPassword = 'password123';
    $hashedPassword = django_make_password($defaultPassword);

    // Check existing username and show result
    $check = $pdo->prepare("SELECT * FROM auth_user WHERE username = ? LIMIT 1");
    $check->execute([$username]);
    $userRow = $check->fetch();

    if ($userRow) {
        echo "<p>User <b>{$username}</b> already exists. Current DB row:</p><pre>";
        print_r($userRow);
        echo "</pre>";
    } else {
        // Build an INSERT that sets typical Django fields to sane defaults.
        // If your table has extra NOT NULL columns, adjust accordingly based on the column list above.
        $insertSql = "
            INSERT INTO auth_user (
                password, last_login, is_superuser, username,
                first_name, last_name, email, is_staff, is_active, date_joined
            ) VALUES (
                ?, NULL, 1, ?, '', '', '', 1, 1, NOW()
            )
        ";
        try {
            $stmt = $pdo->prepare($insertSql);
            $stmt->execute([$hashedPassword, $username]);
            echo "<p style='color:green'>Inserted user <b>{$username}</b> successfully.</p>";
        } catch (PDOException $e) {
            echo "<div style='color:red'><b>Insert failed:</b><br>";
            echo "SQLSTATE: " . htmlspecialchars($e->getCode()) . "<br>";
            echo "Message: " . htmlspecialchars($e->getMessage()) . "</div>";
            // Dump last attempted SQL and params for debugging (safe on dev, not production)
            echo "<pre>Attempted SQL:\n" . $insertSql . "\nParams:\n";
            print_r([$hashedPassword, $username]);
            echo "</pre>";
            exit;
        }
    }

    // 3) Extra: verify the stored hash (basic)
    $row = $pdo->query("SELECT username, password FROM auth_user WHERE username = " . $pdo->quote($username) . " LIMIT 1")->fetch();
    if ($row) {
        echo "<p>Stored password field for <b>{$username}</b>:</p><pre>" . htmlspecialchars($row['password']) . "</pre>";

        // Provide a PHP function to verify Django pbkdf2_sha256 hashes
        function django_check_password($password, $django_hash) {
            $parts = explode('$', $django_hash);
            if (count($parts) !== 4) return false;
            list($algorithm, $iterations, $salt, $b64hash) = $parts;
            if ($algorithm !== 'pbkdf2_sha256') return false;
            $raw = hash_pbkdf2('sha256', $password, $salt, (int)$iterations, 32, true);
            return hash_equals(base64_encode($raw), $b64hash);
        }

        $ok = django_check_password($defaultPassword, $row['password']) ? 'YES' : 'NO';
        echo "<p>Does <b>{$defaultPassword}</b> verify against stored hash? <b>{$ok}</b></p>";
    }

} catch (PDOException $e) {
    echo "<div style='color:red'><b>Connection failed:</b> " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>
