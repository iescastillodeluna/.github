<?php

require_once __DIR__ . '/../core/WebhookHandlerInterface.php';

class RepositoryDeletedHandler implements WebhookHandlerInterface {
    public static function handle(array $data, string $token): array {
        $repoName = $data['repository']['name'];
        $sender = $data['sender']['login'] ?? '';

        // La eliminación de los repositorios de estudiante la hace
        // el propio Classroom, así que tienen este sender
        $isClassroomRepo = $sender === 'github-classroom[bot]';

        if($isClassroomRepo) {
            return [
                'code'     => 200,
                'message'  => "Repositorio de classroom '{$repoName}' no necesita gestión al eliminarse",
                'status'   => 'OK'
            ];
        }

        // La plantilla la crea automáticamente Classroom, que acaba
        // siempre así la descripción.
        $description = $data['repository']['description'] ?? '';
        $isTemplate = str_ends_with($description, 'created by GitHub Classroom');

        $apiResponse = call_github_api([
            'event_type' => $isTemplate?'template-repo-deleted':'member-repo-deleted',
            'client_payload' => $data
        ], $token);

        return ($apiResponse['code']>=200 && $apiResponse['code'] < 300)
            ? [
                'code'    => 200,
                'status'  => "OK",
                'message' => "Workflow lanzado al eliminar el repositorio '{$repoName}'"
              ]
            : [
                'code'    => $apiResponse['code'],
                'status'  => 'ERROR',
                'message' => "No se activa workflow: {$apiResponse['response']}"
              ];
    }
}
