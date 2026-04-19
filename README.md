# Droplog PHP SDK

Official PHP SDK for [Droplog](https://droplog.dev) — audit log infrastructure for SaaS.

## Requirements

- PHP 8.1+
- `ext-curl`
- `ext-json`

## Installation

```bash
composer require droplog/droplog
```

## Usage

### Initialization

```php
use Droplog\Droplog;

$droplog = new Droplog('sk_live_ca_...');

// Or via environment variable DROPLOG_API_KEY
$droplog = new Droplog();
```

Options:

```php
$droplog = new Droplog('sk_live_ca_...', [
    'base_url' => 'https://api.droplog.dev', // default
    'timeout'  => 30,                         // seconds, default 30
]);
```

### Track an event

```php
$event = $droplog->track([
    'tenant_id' => 'tenant_123',
    'actor'     => [
        'id'    => 'usr_456',
        'name'  => 'Alice Dupont',
        'email' => 'alice@example.com',
        'role'  => 'admin',
    ],
    'action'   => 'document.created',
    'resource' => [
        'id'   => 'doc_789',
        'type' => 'document',
        'name' => 'Q1 Report',
    ],
    'metadata' => ['size_kb' => 42],
]);
```

### Track multiple events (batch)

```php
$result = $droplog->trackBatch([
    [
        'tenant_id' => 'tenant_123',
        'actor'     => ['id' => 'usr_456'],
        'action'    => 'user.login',
    ],
    [
        'tenant_id' => 'tenant_123',
        'actor'     => ['id' => 'usr_456'],
        'action'    => 'document.deleted',
        'resource'  => ['id' => 'doc_001', 'type' => 'document'],
    ],
]);

// $result['data']  — array of created events
// $result['total'] — count
```

### List events

```php
$page = $droplog->events->list([
    'tenant_id' => 'tenant_123',
    'action'    => 'user.*',
    'limit'     => 20,
    'offset'    => 0,
]);

// $page['data']   — array of events
// $page['total']  — total matching events
// $page['limit']  — applied limit
// $page['offset'] — applied offset
```

### Get a single event

```php
$event = $droplog->events->get('evt_...');
```

### Viewer sessions

Create a shareable link for a tenant's audit log:

```php
$session = $droplog->viewers->create('tenant_123', ttl: 3600);

// $session['url']        — https://view.droplog.dev/view/tok_...
// $session['expires_at'] — ISO 8601 expiry
```

```php
$sessions = $droplog->viewers->list();

$deleted = $droplog->viewers->delete('ses_...');
```

### Alert rules

```php
$rule = $droplog->alertRules->create(['action_pattern' => 'user.*']);

$rules = $droplog->alertRules->list();

$deleted = $droplog->alertRules->delete('alr_...');
```

## Error handling

```php
use Droplog\DroplogException;

try {
    $droplog->track([...]);
} catch (DroplogException $e) {
    echo $e->getMessage(); // human-readable message from the API
    echo $e->status;       // HTTP status code (401, 429, etc.)
}
```

## License

MIT
