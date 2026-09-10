# Changelog

All notable changes to this project will be documented in this file.

## [1.2.0] - 2026-09-10

### Features

- **api-client:** Expose RateLimit-* response headers via Context7Service::lastRateLimit()

## [1.1.0] - 2026-09-10

### Bug Fixes

- Track composer.lock

### Features

- **auth:** Support --from-env and an interactive prompt in auth:save

### Miscellaneous Tasks

- Add FUNDING.yml

## [1.0.0] - 2026-09-10

### Bug Fixes

- First auto-versioned release should be 1.0.0, not 0.0.1

## [0.0.1] - 2026-09-10

### Miscellaneous Tasks

- Scaffold Laravel Zero CLI project

### Other

- Ajuste do gitignore
- Implement Context7 API integration

Add commands covering the full Context7 v2 API (search, docs, metrics,
refresh, policies, and add:github/gitlab/bitbucket/git/openapi/llmstxt/
website), API-key auth storage, and matching Pest coverage. Also fixes
release.yml to the current draft-then-publish scaffold pattern and adds
the project's portfolio banner.

Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01YW92zEdLtfSDuAzpZNFqFy


