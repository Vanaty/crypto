<?php

$url = "http://localhost:8081"; // Remplacez par l'URL de votre requête

while (true) {
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        echo "Erreur cURL : " . curl_error($ch) . "\n";
    } else {
        echo "[" . date("H:i:s") . "] HTTP $http_code : " . substr($response, 0, 100) . "\n";
    }

    curl_close($ch);

    sleep(10);
}
