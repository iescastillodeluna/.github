<?php

// Carga variables de entorno relacionadas con GitHub
function load_github_env() {
    return [
        'secret' => getenv('GITHUB_SECRET'),
        'token' => getenv('GITHUB_TOKEN')
    ];
}

// Registra información de depuración
function log_github_event(string $logFile, array $headers, string $payload): void {
    file_put_contents(
        $logFile,
        "======= Nueva solicitud =======\n" .
        "Headers:\n" . print_r($headers, true) . "\n" .
        "Payload:\n" . $payload . "\n\n",
        FILE_APPEND
    );
}

// Valida la firma HMAC del webhook
function validate_webhook(string $secret, string $payload, string $signature): bool {
    if(DEV_MODE) return true; // Modo de prueba
    $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    return hash_equals($hash, $signature);
}

// Ejecuta una petición a la API de GitHub
function call_github_api(array $data, string $token): array {
    $org = $data['client_payload']['organization']['login'];
    $url = "https://api.github.com/repos/{$org}/".REPO_ACTIONS."/dispatches";
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept: application/vnd.github.v3+json',
            'Content-Type: application/json',
            'User-Agent: PHP-Script'
        ],
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);
    
    $response = curl_exec($ch);
    if($response === false) {
       $response = curl_error($ch);
       $httpCode = 500;
    }
    else {
       $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }

    curl_close($ch);
    return ['code' => $httpCode, 'response' => $response];
}
