<?php

namespace Droplog\Resources;

use Droplog\Droplog;

class AlertRulesResource
{
    public function __construct(private readonly Droplog $client) {}

    public function list(): array
    {
        return $this->client->request('GET', '/v1/alert-rules');
    }

    public function create(string $actionPattern): array
    {
        return $this->client->request('POST', '/v1/alert-rules', body: ['action_pattern' => $actionPattern]);
    }

    public function delete(string $ruleId): void
    {
        $this->client->request('DELETE', "/v1/alert-rules/{$ruleId}");
    }
}
