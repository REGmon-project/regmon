# PHP security update (October 2026)

The Docker image uses PHP 8.2.35, pinned to the official multi-platform image
index digest in `Dockerfile`. This release fixes CVE-2026-109618 and
CVE-2026-109619. Apache/mod_php is affected; a TLS reverse proxy does not
replace the PHP update. See the [PHP security advisory](https://www.php.net/archive/2026.php#2026-10-21-5).

Both `cfg/php.production.ini` and `cfg/php.development.ini` disable
`session.upload_progress.enabled` before request processing. This follows
PHP's [23 October follow-up advice](https://www.php.net/archive/2026.php#2026-10-23-1).
It disables PHP's session-based upload-progress tracking, not file uploads.
Keep it disabled pending review of the subsequent PHP fix. It is an additional
precaution, not a substitute for the patched runtime.

## Rebuild and verify

Run from the repository root with the deployment's normal environment and
secret files available. The explicit Compose file avoids automatically loading
a local development override. If your deployment uses additional Compose files,
include the same files for every command below.

```sh
docker compose -f docker-compose.yml build --pull app

docker compose -f docker-compose.yml run --rm --no-deps -e XDEBUG_MODE=off app \
  php -r '
    printf("PHP %s; session.upload_progress.enabled=%s\n", PHP_VERSION,
      ini_get("session.upload_progress.enabled") ?: "0");
    if (PHP_VERSION_ID < 80235 || PHP_VERSION_ID >= 80300 ||
        filter_var(ini_get("session.upload_progress.enabled"), FILTER_VALIDATE_BOOLEAN)) {
      fwrite(STDERR, "Unexpected PHP runtime or upload-progress setting\n");
      exit(1);
    }
  '
```

The expected runtime is PHP 8.2.35 and upload progress must be disabled. Test
both `STAGE=production` and `STAGE=development` locally, since Compose selects
the corresponding PHP configuration file.

After successful verification, recreate the PHP service to replace all old
Apache/PHP workers:

```sh
docker compose -f docker-compose.yml up -d --no-deps --force-recreate app
docker compose -f docker-compose.yml exec -e XDEBUG_MODE=off app \
  php -r 'echo PHP_VERSION, "\n";'
```

Verify normal login, database access, uploads and exports after recreation.
These commands do not require rebuilding or recreating the database. Do not
remove database or dependency volumes as part of this runtime update. Restarting
an existing container alone does not replace the PHP binary.

For the next PHP patch, update the version and index digest together, rebuild,
and repeat verification. Digest pinning does not automatically install later
security fixes. PHP 8.2 reaches the end of security support on 31 December 2026;
plan a separately tested upgrade to a supported branch.

## Apply to derived repositories

Keep this change in a focused commit containing the Dockerfile, the two PHP
configuration files, and this document. It does not depend on the origin
repository's ports, network layout or dependency-volume names.

From a clean derived checkout, fetch the local origin checkout and cherry-pick
the security commit, substituting its actual hash:

```sh
git fetch /home/ken/Code/PHP/regmon security/php-8.2.35
git cherry-pick -x <security-commit>
```

Alternatively, export the commit with `git format-patch -1 --stdout <security-commit>`
and apply that patch with `git am`. A cherry-pick or patch applies source changes;
the derived application image must still be rebuilt, verified and recreated.
