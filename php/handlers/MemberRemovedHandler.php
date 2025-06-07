<?php

require_once __DIR__ . '/../core/WebhookHandlerInterface.php';

class MemberRemovedHandler implements WebhookHandlerInterface {
    public static function handle(array $data, string $token): array {
        $user_login = $data['membership']['user']['login'];

        $apiResponse = call_github_api([
            'event_type' => 'member-removed',
            'client_payload' => $data
        ], $token);

        return ($apiResponse['code']>=200 && $apiResponse['code'] < 300)
            ? [
                'code'    => 200,
                'status'  => "OK",
                'message' => "Workflow lanzado al eliminar el miembro '{$user_login}'"
              ]
            : [
                'code'    => $apiResponse['code'],
                'status'  => 'ERROR',
                'message' => "No se activa workflow: {$apiResponse['response']}"
              ];
    }
}
