<?php

require_once __DIR__ . '/../core/WebhookHandlerInterface.php';

class PushRepositoryHandler implements WebhookHandlerInterface {
    public static function handle(array $data, string $token): array {
        $isChanged = false;
        if($data['repository']['name'] == '.github') {
            $modified = array_merge(
                $data['head_commit']['modified'],
                $data['head_commit']['added'],
                $data['head_commit']['removed']
            );
            foreach($modified as $path) {
                if($isChanged = str_starts_with($path, '.github/')) break;
            } 
        }

        if(!$isChanged) {
            return [
                'code'    => 200,
                'status'  => "OK",
                'message' => "No se activa workflow: el cambio no afecta a .github/"
            ];
        }

        $apiResponse = call_github_api([
            'event_type' => 'push-dotgithub',
            'client_payload' => [
                'public_repo' => $data['repository']['full_name'],
                'organization' => $data['organization'],
                'commit' => [
                    'modified' => $data['head_commit']['modified'],
                    'added' => $data['head_commit']['added'],
                    'removed' => $data['head_commit']['removed']
                ]
            ]
        ], $token);

        return ($apiResponse['code']>=200 && $apiResponse['code'] < 300)
            ? [
                'code'    => 200,
                'status'  => "OK",
                'message' => "Workflow lanzado al modificar el repositorio"
              ]
            : [
                'code'    => $apiResponse['code'],
                'status'  => 'ERROR',
                'message' => "No se activa workflow: {$apiResponse['response']}"
              ];
    }
}
