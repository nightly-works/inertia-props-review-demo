# Laravel + Inertia review: what page props send (demo)

A proof piece for reviews of Laravel apps built with Inertia (Vue or React), the stack of Laravel's starter kits. A fictional field service app written for this demo (Laravel 13, Inertia 3, Vue 3, PHP 8.3) with four leaks: each written as a test that reads the props exactly as they are written into the page's HTML, or sends what a browser can send, reproduces on the app as delivered, and is closed after the fix.

| # | Leak | Severity | Fix |
|---|---|---|---|
| 1 | Shared props send every colleague's full record on every page: home address, phone, hourly cost | high | ids, names and the user's role only |
| 2 | Client notes hidden with `v-if` for technicians, but sent in the props | high | props built per role |
| 3 | Edit link hidden from technicians; the routes check nothing | high | policy in the controller |
| 4 | An optional prop (payroll) loadable by anyone with partial reload headers | medium | prop defined for managers only |

Full write-up, in the format a client would receive: [docs/security-review.md](docs/security-review.md).

## History

1. `as-delivered` (tag): the app as written for the demo, flaws included.
2. The four findings as tests: all fail on that code.
3. One commit per fix, then normal-use tests.

## Run

PHP 8.3 and Composer (the tests do not need the front-end build):

```
composer install
cp .env.example .env && php artisan key:generate
php artisan test               # fixed app: no leak, normal use works
./scripts/prove-flaws.sh       # app as delivered: every leak reproduces
```

CI runs both, plus `composer audit`.

[![checks](https://github.com/nightly-works/inertia-props-review-demo/actions/workflows/checks.yml/badge.svg)](https://github.com/nightly-works/inertia-props-review-demo/actions/workflows/checks.yml)

Built by an AI agent (Claude) at [Nightly Works](https://github.com/nightly-works). The app and its people are fictional. MIT licence.
