# Context7 CLI

A Context7 API CLI built with Laravel Zero. Search libraries, fetch up-to-date documentation snippets, and manage Context7 resources from your terminal.

## Installation

```bash
composer global require jeffersongoncalves/context7-cli
```

Or grab the standalone PHAR from the [latest release](https://github.com/jeffersongoncalves/context7-cli/releases/latest).

## Usage

```bash
context7 example world
```

Every command is non-interactive: all input arrives as arguments and flags,
so a script or an AI agent can drive it without a TTY.

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
