<div class="filament-hidden">

![Context7 CLI](https://raw.githubusercontent.com/jeffersongoncalves/context7-cli/main/art/jeffersongoncalves-context7-cli.png)

</div>

# Context7 CLI

[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20A%20Coffee-support-FFDD00?style=flat-square&logo=buy-me-a-coffee&logoColor=black)](https://buymeacoffee.com/jeffersongoncalves)

A Context7 API CLI built with [Laravel Zero](https://laravel-zero.com). Search libraries, fetch up-to-date documentation snippets, and manage Context7 resources from your terminal.

<p align="center">
  <a href="https://github.com/jeffersongoncalves/context7-cli/actions"><img src="https://github.com/jeffersongoncalves/context7-cli/actions/workflows/tests.yml/badge.svg" alt="Tests" /></a>
  <a href="https://packagist.org/packages/jeffersongoncalves/context7-cli"><img src="https://img.shields.io/packagist/dt/jeffersongoncalves/context7-cli" alt="Total Downloads" /></a>
  <a href="https://github.com/jeffersongoncalves/context7-cli/blob/main/LICENSE"><img src="https://img.shields.io/github/license/jeffersongoncalves/context7-cli" alt="License" /></a>
  <img src="https://img.shields.io/badge/php-%3E%3D8.3-8892BF" alt="PHP 8.3+" />
</p>

## Installation

```bash
composer global require jeffersongoncalves/context7-cli
```

Or grab the standalone PHAR from the [latest release](https://github.com/jeffersongoncalves/context7-cli/releases/latest).

## Usage

Every command takes its input as arguments and flags, so a script or an AI
agent can drive it without a TTY — `auth:save` is the only exception, which
falls back to an interactive password prompt when run without an argument
or `--from-env`.

### Authentication

An API key raises your rate limit and is required for `refresh`. Get one at
[context7.com/dashboard](https://context7.com/dashboard).

```bash
context7 auth:save ctx7sk-your-api-key   # pass it directly
context7 auth:save                       # or get prompted for it interactively
CONTEXT7_API_KEY=ctx7sk-your-api-key context7 auth:save --from-env   # or read it from the environment
context7 auth:show
context7 auth:forget
```

> **Git Bash on Windows:** a leading `/` in a library ID (e.g. `/vercel/next.js`)
> gets rewritten to a Windows path by MSYS's auto path-conversion. If a command
> fails with `library_not_found`, prefix it with `MSYS_NO_PATHCONV=1`.

### Search for a library

Both the library name and a query (used to rank results by relevance) are required:

```bash
context7 search next.js "how to use the app router"
```

### Fetch documentation

```bash
context7 docs /vercel/next.js "how to use useState"
context7 docs /vercel/next.js "how to use useState" --type=json
```

### Usage metrics

```bash
context7 metrics /vercel/next.js --days=7
```

### Refresh a library's indexed docs (requires an API key)

```bash
context7 refresh /vercel/next.js
context7 refresh /vercel/next.js --branch=canary --git-token=ghp_xxx
```

### Submit a new source for indexing (requires an API key)

```bash
context7 add:github https://github.com/vercel/next.js
context7 add:gitlab https://gitlab.com/org/repo
context7 add:bitbucket https://bitbucket.org/org/repo
context7 add:git https://git.example.com/org/repo --private --generate-docs
context7 add:openapi https://api.example.com/openapi.json
context7 add:llmstxt https://example.com/llms.txt
context7 add:website https://example.com/docs --base-url=https://example.com/docs
```

### Teamspace policies (requires an API key)

```bash
context7 policies:show
context7 policies:update '{"sourceTypes":{"websites":{"enabled":false}}}'
context7 policies:update @path/to/policies.json
```

Update to the latest release (PHAR installs only — Composer installs update
via `composer global update`):

```bash
context7 self-update
context7 self-update --check
```

## Releasing

Run the **Release** workflow from the Actions tab. It stamps `version.txt`,
regenerates `CHANGELOG.md` with git-cliff, builds the PHAR, commits it to
`main`, then creates the tag and release pinned to that exact commit — so a
published tag never moves.

```bash
gh workflow run release.yml --ref main
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please see [SECURITY](.github/SECURITY.md).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
