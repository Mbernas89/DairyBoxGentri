<?php
$serverName = "dairybox.database.windows.net";
$connectionOptions = [
    "Database" => "SOFTENG",
    "UID" => "sqladmin",
    "PWD" => "Password123",
    "Encrypt" => true,
    "TrustServerCertificate" => false
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}
