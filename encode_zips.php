<?php
/*
 * $ php encode.php password zip_folder_path
 */

require_once 'OpenSslCrypto.php';

if (count($argv) < 3) {
    echo "Usage: php encode.php <password> <zip_folder_path> \n";
    exit(1);
}

$password = $argv[1];
$zipFilePath = $argv[2];

foreach (glob($zipFilePath . '/*.zip') as $filePath) {
    echo '- ' . basename($filePath) . PHP_EOL;
    $enc = new OpenSslCrypto($filePath, $password);
    $enc->encode();
}

exit(0);
