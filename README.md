# README

## Configure your Database Engine

### PostgreSQL

If you want to use Docker, copy the `compose.postgresql.yaml.dist` file to `compose.yaml`.

```bash
cp compose.postgresql.yaml.dist compose.yaml
```

Update your `.env` file with the PostgreSQL database URL.

```dotenv
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/medicapp?serverVersion=16&charset=utf8"
```

Activate SQL migration scripts for PostgreSQL.

```yaml
# config/packages/doctrine_migrations.yaml
doctrine_migrations:
    migrations_paths:
        'DoctrineMigrations': '%kernel.project_dir%/migrations/postgres'
    enable_profiler: false
```

Enable PostgreSQL custom ORM functions.

```yaml
# config/packages/doctrine.yaml
doctrine:
    # ...

    orm:
        # ...

        identity_generation_preferences:
            Doctrine\DBAL\Platforms\PostgreSQLPlatform: identity

        # ...

        dql:
            datetime_functions:
                # Postgres Support
                DATE: DoctrineExtensions\Query\Postgresql\Date
                MONTH: DoctrineExtensions\Query\Postgresql\Month
                YEAR: DoctrineExtensions\Query\Postgresql\Year

```

### MySQL / Mariadb

If you want to use Docker, copy the `compose.[mysql|mariadb].yaml.dist` file to `compose.yaml`.

```bash
# For MySQL
$ cp compose.mysql.yaml.dist compose.yaml

# For MariaDB
$ cp compose.mariadb.yaml.dist compose.yaml
```

Update your `.env` file with the MySQL/Mariadb database URL.

```dotenv
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/medicapp?serverVersion=8.0.32&charset=utf8mb4"
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/medicapp?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

Activate SQL migration scripts for MySQL/Mariadb.

```yaml
# config/packages/doctrine_migrations.yaml
doctrine_migrations:
    migrations_paths:
        'DoctrineMigrations': '%kernel.project_dir%/migrations/mysql'
    enable_profiler: false
```

Enable MySQL custom ORM functions.

```yaml
# config/packages/doctrine.yaml
doctrine:
    # ...

    orm:
        # ...

        identity_generation_preferences:
            Doctrine\DBAL\Platforms\MySQLPlatform: identity

        # ...

        dql:
            datetime_functions:
                # MySQL Support
                DATE: DoctrineExtensions\Query\MySQL\Date
                MONTH: DoctrineExtensions\Query\MySQL\Month
                YEAR: DoctrineExtensions\Query\MySQL\Year

```

## Installation

```bash
# If you want to use Docker,
# First, build the environment (database server & mail catcher).
docker compose up --build

# Then, build the demo app (dependencies, database, data fixtures, etc.).
symfony composer dev:install

# Finally, serve the demo app (HTTP Web server).
symfony server:start

# And browse the following URL in your Web browser to discover the app
# https://localhost:8000
```

## Useful Commands

Running PHPUnit with xdebug code coverage.

```bash
# Using the env variable
$ XDEBUG_MODE=coverage (symfony) php bin/phpunit

# Using the php dynamic parameter
$ (symfony) php -d xdebug.mode=coverage bin/phpunit
```

Running Panther in Chrome.

```bash
PANTHER_NO_HEADLESS=1 (symfony) php bin/phpunit
```

Runner PHPUnit with Paratest.

```bash
XDEBUG_MODE=coverage (symfony) php vendor/bin/paratest
```
