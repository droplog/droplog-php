<?php

namespace Droplog\Resources;

use Droplog\Droplog;

class EventsResource
{
    public function __construct(private readonly Droplog $client) {}

    /**
     * @param array{
     *   tenant_id?: string,
     *   actor_id?: string,
     *   action?: string,
     *   from_date?: string,
     *   to_date?: string,
     *   limit?: int,
     *   offset?: int
     * } $params
     */
    public function list(array $params = []): array
    {
        return $this->client->request('GET', '/v1/events', query: $params);
    }

    public function get(string $eventId): array
    {
        return $this->client->request('GET', "/v1/events/{$eventId}");
    }
}
