<?php
/*
 * $ php encode.php password zip_folder_path
 */

require_once 'OpenSslCrypto.php';

if (count($argv) < 3) {
    echo "Usage: php encode.php <password> <zip_input_folder_path> <output_folder_path> \n";
    exit(1);
}

$password = $argv[1];
$zipInputFolderPath = $argv[2];
$outputFolderPath = $argv[3];

foreach (glob($zipInputFolderPath . '/*.zip') as $filePath) {
    echo '- ' . basename($filePath) . PHP_EOL;
    $enc = new OpenSslCrypto($filePath, $password, $outputFolderPath);
    $enc->encode();
}

exit(0);
