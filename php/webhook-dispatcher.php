<?php

require __DIR__ . '/core/GitHubWebhookFunctions.php';
require __DIR__ . '/core/WebhookFactory.php';
require __DIR__ . '/config.inc';

$env = load_github_env();
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
$action = $data['action'] ?? '';

if(LOGFILE) {
   log_github_event(
      LOGFILE,
      getallheaders(),
      $payload
   );
}

if (!validate_webhook($env['secret'], $payload, $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '')) {
    $result = [
        'status' => 'ERROR',
        'code'   => 401,
        'message' => 'Firma inválida'
    ];
}
else if(empty($event)) {
    $result = [
        'status' => 'ERROR',
        'code'   => 400,
        'message' => 'Evento no definido'
    ];
} else if($event === "ping") {
    $result = [
        'status' => 'OK',
        'code'   => 200,
        'message' => 'Ping recibido'
    ];
} else if($handlerClass = WebhookFactory::getHandler($event, $action)) {
    $handlerFile = __DIR__ . "/handlers/{$handlerClass}.php";
    if(file_exists($handlerFile)) {
        require_once $handlerFile;
        $result = $handlerClass::handle($data, $env['token']);
    }
    else {
        $result = [
            'status'  => 'ERROR',
            'code'    => 500,
            'message' => "Manejador para {$event}:{$action} no implementado (archivo faltante)"
        ];
    }
}
else {
    $result = [
        'status' => 'FAIL',
        'code' => 422,
        'message' => "Evento {$event}:{$action} no soportado"
    ];
}

http_response_code($result['code']);
error_log($result['message']);
header('Content-Type: application/json');
echo json_encode($result) . "\n";
