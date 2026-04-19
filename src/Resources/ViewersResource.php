<?php

namespace Droplog\Resources;

use Droplog\Droplog;

class ViewersResource
{
    public function __construct(private readonly Droplog $client) {}

    public function create(string $tenantId, ?int $ttl = null): array
    {
        $body = ['tenant_id' => $tenantId];
        if ($ttl !== null) {
            $body['ttl'] = $ttl;
        }

        return $this->client->request('POST', '/v1/viewer-sessions', body: $body);
    }

    public function list(): array
    {
        return $this->client->request('GET', '/v1/viewer-sessions');
    }

    public function delete(string $sessionId): bool
    {
        $this->client->request('DELETE', "/v1/viewer-sessions/{$sessionId}");

        return true;
    }
}
