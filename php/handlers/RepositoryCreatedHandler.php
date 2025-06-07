<?php

require_once __DIR__ . '/../core/WebhookHandlerInterface.php';

class RepositoryCreatedHandler implements WebhookHandlerInterface {
    public static function handle(array $data, string $token): array {
        $repoName = $data['repository']['name'];
        $isFork = $data['repository']['fork'] ?? false;
        $sender = $data['sender']['login'] ?? '';
        
        // Los repositorios de Classroom siempre tienen este sender
        $isClassroomRepo = $sender === 'github-classroom[bot]';

        // Los repositorios de estudiantes generados con una plantilla son fork.
        // Las plantillas intermedias que crea el bot y los repositorios de
        // estudiantes sin plantilla no lo son; y se distinguen porque los
        // últimos tienen como colaborador externo al alumno. Pero la información
        // no está disponible ahora, así que habrá que presuponer que es una
        // plantilla y terminar la distinción en GitHub.
        $eventType = $isFork ? "student-repo-created" : "template-repo-created";
        
        // Si es un repositorio de estudiante con plantilla, pero el nombre de la tarea
        // empieza por TareaNP, no protegemos el respositorio.
        $filtered = $eventType === "student-repo-created" && str_starts_with(strtolower($repoName), "tareanp");

        if (!$isClassroomRepo) {
            $eventType = "member-repo-created";  // Lo ha creado un miembro.
        } else if ($filtered) {
            return [
                'code'     => 200,
                'message'  => "Repositorio de estudiante '{$repoName}' no se quiere proteger",
                'status'   => 'OK'
            ];
        }

        $apiResponse = call_github_api([
            'event_type' => $eventType,
            'client_payload' => $data
        ], $token);

        return ($apiResponse['code']>=200 && $apiResponse['code'] < 300)
           ? [
                'code'     => 200,
                'status'   => "OK",
                'message'  => "Workflow lanzado al crear el repositorio '{$repoName}'"
             ]
           : [
                'code'    => $apiResponse['code'],
                'status'  => 'ERROR',
                'message' => "No se activa workflow: {$apiResponse['response']}"
             ];
    }
}
