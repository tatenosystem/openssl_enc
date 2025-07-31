<?php
/*
 * 暗号ファイル一覧表示
 * $ php show.php password encrypted_folder_path
 */

require_once 'OpenSslCrypto.php';

if (count($argv) < 3) {
    echo "Usage: php show.php <password> <encrypted_folder_path> \n";
    exit(1);
}

if (!file_exists($argv[2]) || !is_dir($argv[2])) {
    echo "Error: Encrypted folder does not exist: " . $argv[2] . PHP_EOL;
    exit(1);
}

$password = $argv[1];
$encryptedFolderPath = $argv[2];

echo PHP_EOL;
foreach (glob($encryptedFolderPath . '/*') as $filePath) {
    if (!is_file($filePath)) {
        continue;
    }
    if (!preg_match('@\A([0-9a-f]{26})\z$@', basename($filePath))) {
        continue;
    }

    $enc = new OpenSslCrypto($filePath, $password, null, false);
    $realFilePath = $enc->getOutputFilePath(false);
    echo basename($filePath) . '  ' . basename($realFilePath) . PHP_EOL;
}

exit(0);
