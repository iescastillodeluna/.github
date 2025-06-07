<?php

interface WebhookHandlerInterface {
    public static function handle(array $data, string $token): array;
}
