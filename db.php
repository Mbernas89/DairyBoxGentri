<?php
$host = "dairybox.database.windows.net";
$dbname = "SOFTENG";
$user = "sqladmin";
$pass = "Password123";

try {
    $dsn = "sqlsrv:server=$host;Database=$dbname;Encrypt=yes;TrustServerCertificate=no";
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit();
}
