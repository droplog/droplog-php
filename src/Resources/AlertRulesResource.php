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

    /**
     * @param array{action_pattern: string} $params
     */
    public function create(array $params): array
    {
        return $this->client->request('POST', '/v1/alert-rules', body: $params);
    }

    public function delete(string $ruleId): bool
    {
        $this->client->request('DELETE', "/v1/alert-rules/{$ruleId}");

        return true;
    }
}
