<?php
/*
 * $ php encode.php password CL/HOGEHOGE.md
 */

require_once __DIR__ . '/OpenSslCrypto.php';

if (count($argv) < 3) {
    echo "Usage: php encode.php <password> <dataFilePath> \n";
    exit(1);
}

$password = $argv[1];
$dataFilePath = $argv[2];

$enc = new OpenSslCrypto($dataFilePath, $password);
$enc->encode();
exit(0);
