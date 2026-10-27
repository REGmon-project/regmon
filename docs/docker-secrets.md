# Docker secret boundaries

Compose reads the project `.env` file for `${VARIABLE}` interpolation. PHP now
receives only the entries declared in `services.app.environment`; it no longer
imports every `.env` variable through `env_file`. The database password remains
a file mounted at `/run/secrets/mysql_pw`, referenced by `MYSQL_PASSWORD_FILE`.
The database root password is granted only to the database service.

In derived repositories, Python keeps its explicitly declared environment,
including `SECRET_KEY`. PHP does not need that key or a duplicate plaintext
`DB_PASSWORD`. This change does not require changing credentials or the database.

If a custom PHP extension previously relied on another variable from `.env`,
add that specific variable to `services.app.environment`. Do not restore blanket
`.env` injection. Use file-based secrets for additional sensitive values where
the application supports them; `_FILE` is an application convention, not an
automatic conversion performed by Compose.

## Files excluded from builds and Git

`.dockerignore` excludes environment files (including templates), secret
directories, private key files, logs, backup directories and generated dump
files, including nested copies. Database schema and migration `.sql` files
outside backup directories remain available to the builder.

`.gitignore` protects local credentials and generated backups independently of
checkout-local `.git/info/exclude` rules. `.env.sample` remains permitted in Git
for placeholder-only examples. The maintained `backup/backup_script.sh` remains
permitted in Git; the entire backup directory is excluded from Docker builds.

Ignore rules do not untrack previously committed files or change host file
permissions. Protect credential files and their parent directories on the host
while retaining the read access required by the container users. Do not put real
credentials in `.env.sample` or force-add them to Git.

## Activate and verify

Run from the repository root using the deployment's normal environment and
secret files. Include any additional Compose files normally used by that
deployment in every command. Recreate PHP so the old imported environment is
removed from the container:

```sh
docker compose -f docker-compose.yml up -d --no-deps --force-recreate app

docker compose -f docker-compose.yml exec -e XDEBUG_MODE=off app php -r '
  foreach (["SECRET_KEY", "DB_PASSWORD"] as $name) {
    if (getenv($name) !== false) {
      fwrite(STDERR, "Unexpected secret environment variable: " . $name . "\n");
      exit(1);
    }
  }
  $path = getenv("MYSQL_PASSWORD_FILE");
  if (!$path || !is_readable($path)) {
    fwrite(STDERR, "Database secret file is unavailable\n");
    exit(1);
  }
  echo "PHP secret boundary check passed\n";
'
```

Then check login and database access. No database recreation, volume removal,
password rotation or Python service recreation is needed for this change.
Future image builds use the new `.dockerignore`; builds do not remove secrets
from images or artifacts created before these exclusions were added.

The change can be cherry-picked into derived repositories. Preserve their
ports, networks, dependency volumes and Python service configuration.
