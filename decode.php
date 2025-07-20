<?php
/*
 * $ php decode.php password CL/687c5c9f5bf78687c5c9f5bf7b
 */

require_once __DIR__ . '/OpenSslCrypto.php';

if (count($argv) < 3) {
    echo "Usage: php decode.php <password> <dataFilePath> \n";
    exit(1);
}

$password = $argv[1];
$dataFilePath = $argv[2];

$dec = new OpenSslCrypto($dataFilePath, $password);
$dec->decode();
exit(0);
