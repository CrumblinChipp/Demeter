<a>Demeter</a>

Adjust ".env" accordingly
The projet is using Neontech as database.

DB_CONNECTION=pgsql

DB_HOST=ep-divine-wildflower-a1n9jy65-pooler.ap-southeast-1.aws.neon.tech

DB_PORT=5432

DB_DATABASE=neondb

DB_USERNAME=neondb_owner

DB_PASSWORD="endpoint=ep-divine-wildflower-a1n9jy65;npg_69ZhICtGxPzM"

If "artisan serve" wont work, use this to run it on localhost:
php -S localhost:8000 -t public