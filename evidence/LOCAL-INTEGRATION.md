# Local portal integration evidence

Date: 8 November 2026 (Asia/Karachi).
Task baseline: `f4590d7`. Portal baseline: `f124b38`. Historical local results below refer to the integration work before publication; see the hosted verification update for deployed commits and checks.

## Hosted verification — 8 November 2026

This section supersedes historical local-only and Twitter-pending statements below. Mohsin explicitly authorised pushes and deployment of Main and all demos.

- Deployed application commit: `ac965ee` (integration `d6f3ee9` plus Windows SSH-key line-ending normalisation). Manual deployment succeeded: https://github.com/mohsinadeel/task_manager_assignment/actions/runs/42510922808 . Main deployed first and the shared database was backed up before migrations.
- Target: https://demo.mohsinadeel.dev/laravel-task-management-demo/ . Initial condition: new prefixed Task tables, no projects. Real HTTP checks created a uniquely named synthetic project and two tasks, edited a task, sent a reversed order/version to the reorder endpoint, reloaded to confirm persisted order, and deleted both tasks. All passed. Application-owned CSS/JS assets returned 200. Temporary project removed afterwards.
- First-run sample setup: the existing DatabaseSeeder ran only after confirming the task project table was empty; two synthetic projects/five tasks now provide examples. No shared users were seeded.
- Real browser checks: used the move-up control, observed Priority saved, then dragged the second task handle above the first, observed saved priorities and reloaded. Original sample order was restored and persisted. Project dropdown and hosted edit URLs were visible.
- Server checks: tasks_migrations ledger exists independently beside Main, Stripe and Twitter ledgers; the portal symlink resolves only to this demo public directory.
- Local pre-deployment rerun: 16 tests / 81 assertions and production asset build passed using Sail/MySQL. No claim of load testing, multi-user browser concurrency verification or old-data import.
## Scope
Task Management remains Laravel 12.71.0 / Sail PHP 8.3.36 / MySQL 8.4. The portal remains Laravel 13.34.0 / Sail PHP 8.4.26 / MySQL 8.4. Local Compose projects and database volumes are independent. Production is intended to use the existing shared database with `tasks_` table prefixes and a separate prefixed migration ledger.

| Scenario | Starting conditions and action | Expected and observed result | Status / limit |
| --- | --- | --- | --- |
| Task regressions | Local task Sail/MySQL stack; `docker compose exec -T laravel.test php artisan test` | 16 tests / 81 assertions passed: CRUD, priorities, projects, route scoping, escaping, namespace isolation, simulated nested edit URLs and asset paths | Locally verified; no live server claim |
| Shared-schema migration isolation | `DatabaseIsolationTest` creates unrelated unprefixed portal-style and `stripe_`-style probe tables in disposable MySQL `testing`, migrates, rolls back and migrates task-owned tables | Both sentinel records preserved; `tasks_test_projects`, `tasks_test_tasks` and `tasks_test_migrations` created; rollback drops only the task-owned data tables | Locally verified using synthetic sentinels, not a production DB copy |
| Test reset guard | Run PHPUnit without configuration against `DB_DATABASE=task_manager`, then against `DB_DATABASE=testing` with an injected `DB_URL` override | Both requests rejected with the test-harness RuntimeException before refresh traits ran | Expected negative checks; no production connection attempted |
| Existing local data | Normal `php artisan migrate --seed` with `DB_TABLE_PREFIX=tasks_`; inspect local table names | Existing unprefixed migrations/projects/tasks remain; separate tasks_migrations/tasks_projects/tasks_tasks exist | Locally verified; no implicit import of earlier rows |
| Portal cards | `docker compose exec -T -e APP_ENV=testing -e DB_DATABASE=testing laravel.test php artisan test --filter=DemoLandingPageTest` | 2 tests / 11 assertions passed; all three cards, repository URLs, local URLs and hosted task subpath verified | Locally verified; other portal features not retested |
| Builds and style | `npm ci` and `npm run build` inside task Sail; portal `npm run build`; both repositories `vendor/bin/pint --dirty --format agent` and `git diff --check` | Both builds and diff checks passed; changed PHP files formatted. Portal build reported an existing optional fontaine advisory; no dependency was added | Local assets; task Node modules reinstalled from the existing lockfile for Linux |
| HTTP and sessions | GET localhost:8080 and localhost:8082 | Both HTTP 200; portal session cookie demos_session; task cookie task_management_session | Local runtime check; shared generic XSRF cookie names are not claimed to be unique |
| Parent exclusions | `git check-ignore` on nested task files; create and list a tar archive using the portal deploy-exclude file, omitting vendor/node_modules to bound the check | Task checkout ignored by parent Git; neither nested demo included in the archive. Explicit Tailwind source and Vite watcher exclusions added | Archive check excluded dependency directories; no actual upload |
| Manual workflows | Parse main, Stripe and task workflow YAML; `bash -n` on 35 run/remote shell blocks; `php -l` on the task release guard | All six local workflows have only workflow_dispatch; shell and PHP syntax checks passed | No GitHub workflow dispatched; remote Task repository still has its earlier workflow until a later authorised push |
| Hosted deployment | No action | Not run | VPN/SSH secrets, server environment, shared production DB, symlink, hosted assets, CSRF and browser CRUD/reorder still require verification |

## Prepared workflow and outstanding work

Task Management has local manual verification and deployment workflows. Deployment follows the existing VPN/SSH/tar/Laravel pattern, builds production assets, validates the task namespace and file-session cookie before Artisan release commands, applies normal migrations and creates only the public-directory symlink. It requires production-environment variables/secrets documented in README, including an independently verified SSH_KNOWN_HOSTS value. Sample data is not automatically seeded.

Twitter remains linked to its existing deployment. Its repository currently has an automatic push-triggered FTPS workflow; moving it to the shared host requires separate database/authentication compatibility work. Preparing its matching local workflow is pending Mohsin's checkout-path answer. No Twitter checkout or source changes were made.

The existing public task repository name `task_manager_assignment` predates applying this project's `demo` naming rule. No remote rename was made. Decide the public slug before a later publication/deployment step if that naming rule is to be enforced.
