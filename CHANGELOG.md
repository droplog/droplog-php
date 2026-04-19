# Changelog

## [0.1.2] - 2026-04-19

### Changed
- `AlertRulesResource::create()` now accepts an `array` instead of a bare `string` to allow future fields without breaking changes
- `ViewersResource::delete()` and `AlertRulesResource::delete()` now return `bool` instead of `void`
- Timeout is now configurable via `$options['timeout']` (default: 30s)
- Added explicit `CURLOPT_HTTPGET` for GET requests

### Fixed
- Removed `"version"` field from `composer.json` (managed by Git tags on Packagist)

## [0.1.1] - 2026-04-18

### Added
- `Droplog::track()` — ingest a single event
- `Droplog::trackBatch()` — ingest up to 100 events in one request
- `EventsResource` — `list()`, `get()`
- `ViewersResource` — `create()`, `list()`, `delete()`
- `AlertRulesResource` — `list()`, `create()`, `delete()`
- `DroplogException` with `$status` property
- Zero external dependencies (native `ext-curl` + `ext-json`)

## [0.1.0] - 2026-04-18

### Added
- Initial release (minimal structure)
- `Droplog::track()` — ingest a single event
- `Droplog::trackBatch()` — ingest up to 100 events in one request
- `EventsResource` — `list()`, `get()`
- `ViewersResource` — `create()`, `list()`, `delete()`
- `AlertRulesResource` — `list()`, `create()`, `delete()`
- `DroplogException` with `$status` property
- Zero external dependencies (native `ext-curl` + `ext-json`)
