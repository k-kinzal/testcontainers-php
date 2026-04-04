# Security Policy

## Supported Versions

Security fixes are developed on `main` and shipped in the latest release line.

| Version | Supported |
| --- | --- |
| `main` | Yes |
| Latest release | Yes |
| Older releases | Best effort |

This project intentionally supports PHP 5.6 through 8.5, including EOL PHP versions.
That compatibility goal affects how security fixes are delivered:

- We try to preserve the supported PHP matrix declared by the project.
- For EOL PHP runtimes, upstream dependencies and tooling may no longer publish patched releases.
- Security fixes for issues that only affect EOL PHP runtimes are not guaranteed and may not be provided.
- If no compatible upstream fix exists for an EOL runtime, users must upgrade PHP to a supported runtime to receive a fix.
- We may mark an affected runtime/dependency combination as unsupported.

## Reporting a Vulnerability

Do not open a public issue for suspected security vulnerabilities.

Preferred reporting paths:

1. Use GitHub Private Vulnerability Reporting from the repository's Security tab when it is enabled.
2. If private reporting is unavailable, contact the maintainer at `keen.flag7803@logn.in`.

Please include:

- Affected version, tag, or branch
- PHP version
- Docker or CI environment details
- Reproduction steps or proof of concept
- Expected impact

## Scope of GitHub-Native Dependency Tooling

GitHub's dependency graph and Dependabot recognize Composer via the standard
`composer.lock` and `composer.json` files. This repository also maintains
per-PHP lockfiles such as `composer-5.6.lock` through `composer-8.5.lock`
to preserve compatibility across the full runtime matrix.

As a result:

- Dependabot and dependency alerts are only a partial signal for Composer dependencies in this repository.
- GitHub Actions dependencies are a good fit for Dependabot and are managed automatically.
- Composer dependency changes still require maintainer review across the full PHP matrix before merge.
