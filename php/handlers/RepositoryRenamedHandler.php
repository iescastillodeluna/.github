<?php

require_once __DIR__ . '/../core/WebhookHandlerInterface.php';

class RepositoryRenamedHandler implements WebhookHandlerInterface {
    public static function handle(array $data, string $token): array {
        $repoName = $data['repository']['name'];
        $sender = $data['sender']['login'] ?? '';

        // Los repositorios de Classroom siempre tienen este sender
        $isClassroomRepo = $sender === 'github-classroom[bot]';

        if($isClassroomRepo) {
            return [
                'code'     => 200,
                'message'  => "Repositorio de classroom '{$repoName}' no necesita gestión al renombrarse",
                'status'   => 'OK'
            ];
        }

        $apiResponse = call_github_api([
            'event_type' => 'repo-renamed',
            'client_payload' => $data
        ], $token);

        return ($apiResponse['code']>=200 && $apiResponse['code'] < 300)
            ? [
                'code'    => 200,
                'status'  => "OK",
                'message' => "Workflow lanzado al renombrar el repositorio '{$repoName}'"
              ]
            : [
                'code'    => $apiResponse['code'],
                'status'  => 'ERROR',
                'message' => "No se activa workflow: {$apiResponse['response']}"
              ];
    }
}
