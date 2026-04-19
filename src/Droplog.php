<?php

namespace Droplog;

use Droplog\Resources\AlertRulesResource;
use Droplog\Resources\EventsResource;
use Droplog\Resources\ViewersResource;

class Droplog
{
    public const VERSION = '0.1.2';
    private const DEFAULT_BASE_URL = 'https://api.droplog.dev';
    private const DEFAULT_TIMEOUT = 30;

    private string $apiKey;
    private string $baseUrl;
    private int $timeout;

    public readonly EventsResource $events;
    public readonly ViewersResource $viewers;
    public readonly AlertRulesResource $alertRules;

    public function __construct(?string $apiKey = null, array $options = [])
    {
        $apiKey = $apiKey ?? (getenv('DROPLOG_API_KEY') ?: null);

        if (!$apiKey) {
            throw new \InvalidArgumentException(
                'No API key provided. Pass it as the first argument or set the DROPLOG_API_KEY environment variable.'
            );
        }

        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($options['base_url'] ?? self::DEFAULT_BASE_URL, '/');
        $this->timeout = $options['timeout'] ?? self::DEFAULT_TIMEOUT;

        $this->events = new EventsResource($this);
        $this->viewers = new ViewersResource($this);
        $this->alertRules = new AlertRulesResource($this);
    }

    /**
     * Track a single event.
     *
     * @param array{
     *   tenant_id: string,
     *   actor: array{id: string, name?: string|null, email?: string|null, role?: string|null},
     *   action: string,
     *   resource?: array{id?: string|null, type?: string|null, name?: string|null}|null,
     *   metadata?: array<string, mixed>|null,
     *   ip_address?: string|null,
     *   occurred_at?: string|null
     * } $params
     */
    public function track(array $params): array
    {
        return $this->request('POST', '/v1/events', body: $this->mapEventParams($params));
    }

    /**
     * Track multiple events in a single request (max 100).
     *
     * @param list<array> $events Each element has the same shape as track() params.
     */
    public function trackBatch(array $events): array
    {
        $mapped = array_map([$this, 'mapEventParams'], $events);

        return $this->request('POST', '/v1/events/batch', body: ['events' => $mapped]);
    }

    /**
     * @internal Used by resource classes.
     */
    public function request(string $method, string $path, array $body = [], array $query = []): array
    {
        $url = $this->baseUrl . $path;

        if ($query) {
            $url .= '?' . http_build_query(array_filter($query, fn ($v) => $v !== null));
        }

        $headers = [
            'Content-Type: application/json',
            'X-API-Key: ' . $this->apiKey,
            'User-Agent: droplog-php/' . self::VERSION,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_THROW_ON_ERROR));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } else {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new DroplogException("HTTP request failed: {$curlError}", 0);
        }

        if ($statusCode === 204) {
            return [];
        }

        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if ($statusCode >= 400) {
            $message = $data['error']['message'] ?? "HTTP {$statusCode}";
            throw new DroplogException($message, $statusCode);
        }

        return $data;
    }

    private function mapEventParams(array $params): array
    {
        $now = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format(\DateTimeInterface::RFC3339);

        return [
            'tenant_id'     => $params['tenant_id'],
            'actor_id'      => $params['actor']['id'],
            'actor_name'    => $params['actor']['name'] ?? null,
            'actor_email'   => $params['actor']['email'] ?? null,
            'actor_role'    => $params['actor']['role'] ?? null,
            'action'        => $params['action'],
            'resource_id'   => $params['resource']['id'] ?? null,
            'resource_type' => $params['resource']['type'] ?? null,
            'resource_name' => $params['resource']['name'] ?? null,
            'metadata'      => $params['metadata'] ?? new \stdClass(),
            'ip_address'    => $params['ip_address'] ?? null,
            'occurred_at'   => $params['occurred_at'] ?? $now,
        ];
    }
}
