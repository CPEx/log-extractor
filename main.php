<?php
require "vendor/autoload.php";

// read the configuration file
$dataDir = getenv('KBC_DATADIR') . DIRECTORY_SEPARATOR;
$configFile = $dataDir . 'config.json';
$configAll = json_decode(file_get_contents($configFile), true);
$config = $configAll['parameters'];

if (!isset($config['outputFile'])) {
    $config['outputFile'] = 'accesslog.csv';
}

try {
    $url = $config['url'] . '?' . implode('&', $config['urlParams']);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if (isset($config['timeout'])) {
        curl_setopt($ch, CURLOPT_TIMEOUT, intval($config['timeout']));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    $result = curl_exec($ch);
    curl_close($ch);

    if ($result) {
        file_put_contents($config['outputFile'], $result);
    } else {
        echo 'No response from ' . $url;
        exit(3);
    }


} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    exit(1);
} catch (\Throwable $e) {
    echo $e->getMessage();
    exit(2);
}