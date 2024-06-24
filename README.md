# symfony-short

## Project


## Database Configuration
To configure the database connection, update the `.env` file with the appropriate credentials:
```bash
DATABASE_URL=mysql://username:password@localhost:3306/database_name
```

## Starting the Server
To start the server, run the following command:
```bash
symfony server:start
```

## Migration
To generate a new migration, use the following command:
```bash
php bin/console make:migration
```

To execute the migrations, run:
```bash
php bin/console doctrine:migrations:migrate
```

To rollback the last applied migration, use:
```bash
php bin/console doctrine:migrations:execute --down <version>
```

To check the current status of migrations, run:
```bash
php bin/console doctrine:migrations:status
```