<?php

class WebhookFactory {
    private const HANDLER_MAP = [
        'repository' => [
            'created' => 'RepositoryCreatedHandler',
	    'deleted' => 'RepositoryDeletedHandler',
	    'renamed' => 'RepositoryRenamedHandler'
        ],
        'organization' => [
            'member_removed' => 'MemberRemovedHandler'
        ]
    ];

    public static function getHandler(string $event, string $action): ?string {
        return self::HANDLER_MAP[$event][$action] ?? null;
    }
}
