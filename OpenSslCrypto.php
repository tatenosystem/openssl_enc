<?php
/*
 * OpenSslCrypto
 * OpenSSLでファイルを暗号化/復号化するクラス
 * パスワードを指定して、ファイルを暗号化/復号化する
 */

class OpenSslCrypto
{
    const CRYPTO_FILE_NAME = 'openssl_crypto.enc';
    const CRYPTO_FILE_NAME_TMP = 'openssl_crypto_tmp_.txt';

    private $dataFilePath;
    private $dataPath;
    private $password;
    private $cryptoFilePath;
    private $cryptoFilePathTmp;

    public function __construct($dataFilePath, $password, $outputFolderPath = null)
    {
        $this->dataFilePath = $dataFilePath;
        if ($outputFolderPath === null) {
            $this->dataPath = dirname($dataFilePath);
        } else {
            $this->dataPath = $outputFolderPath;
        }
        $this->password = $password;
        $this->cryptoFilePath = $this->dataPath . '/' . self::CRYPTO_FILE_NAME;
        $this->cryptoFilePathTmp = $this->dataPath . '/' . self::CRYPTO_FILE_NAME_TMP;
    }

    private function decodeCryptoListFile()
    {
        if (!file_exists($this->cryptoFilePath)) {
            touch($this->cryptoFilePath);
            return '';
        }
        $command = sprintf(
            'openssl enc -d -aes256 -pbkdf2 -md sha-256 -k "%s" -in "%s" -out "%s"',
            $this->password,
            $this->cryptoFilePath,
            $this->cryptoFilePathTmp
        );
        $result = shell_exec($command . ' 2>&1');
        $this->checkResult($this->cryptoFilePathTmp, $result);
    }

    private function encodeCryptoListFile()
    {
        $command = sprintf(
            'openssl enc -aes256 -pbkdf2 -md sha-256 -k "%s" -in "%s" -out "%s"',
            $this->password,
            $this->cryptoFilePathTmp,
            $this->cryptoFilePath
        );
        $result = shell_exec($command . ' 2>&1');
        $this->checkResult($this->cryptoFilePathTmp, $result);
    }


    private function getOutputFilePath($isEncode)
    {
        if (!file_exists($this->dataFilePath)) {
            echo "\nInput file does not exist: " . $this->dataFilePath . "\n\n";
            exit(1);
        }

        $targetFileName = basename($this->dataFilePath);
        $this->decodeCryptoListFile();

        if (!file_exists($this->cryptoFilePathTmp)) {
            touch($this->cryptoFilePathTmp);
        }

        $listFiles = file($this->cryptoFilePathTmp);
        foreach ($listFiles as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $elements = explode("\t", $line);
            if ($isEncode && $elements[0] === $targetFileName) {
                return $this->dataPath . '/' . $elements[1];
            }
            if (!$isEncode && $elements[1] === $targetFileName) {
                return $this->dataPath . '/' . $elements[0];
            }
        }

        if ($isEncode) {
            $newFileName = uniqid() . uniqid();
            file_put_contents($this->cryptoFilePathTmp, $targetFileName . "\t" . $newFileName . "\n", FILE_APPEND);
            $this->encodeCryptoListFile();
        } else {
            throw new \RuntimeException('Encode File Not Found: ' . $targetFileName);
        }

        return $this->dataPath . '/' . $newFileName;
    }

    public function encode()
    {
        $outputFilePath = $this->getOutputFilePath(true);

        $command = sprintf(
            'openssl enc -aes256 -pbkdf2 -md sha-256 -k "%s" -in "%s" -out "%s"',
            $this->password,
            $this->dataFilePath,
            $outputFilePath
        );

        $result = shell_exec($command . ' 2>&1');
        $this->checkResult($outputFilePath, $result, 'File Encrypted');
    }

    public function decode()
    {
        $outputFilePath = $this->getOutputFilePath(false);
        $command = sprintf(
            'openssl enc -d -aes256 -pbkdf2 -md sha-256 -k "%s" -in "%s" -out "%s"',
            $this->password,
            $this->dataFilePath,
            $outputFilePath
        );

        $result = shell_exec($command . ' 2>&1');
        $this->checkResult($outputFilePath, $result, 'File Decrypted');
    }

    private function checkResult($outputPath, $result, $message = '')
    {
        if (file_exists($outputPath)) {
            echo "Success: " . $message . " " . $outputPath . "\n";
        } else {
            echo "\n[Error] " . $message . " failed " . $outputPath . "\n\n";
            if ($result) echo "OpenSSL output: $result\n";
            throw new \RuntimeException($message . ' failed: ' . $outputPath);
        }
    }
}
