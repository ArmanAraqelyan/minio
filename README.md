## About Project

The CSV file is uploaded to the storage (minio) every day at 00:01.
This is a service where the program adds the USD column to the end of the CSV file calculated from the RUB column.

## Usage

You need to do the following steps:
- make bucket into the storage (minio)
- configure env file
- configure config/filesystems.php file
- configure config/queue.php file
- php artisan queue:table
- php artisan migrate
- php artisan queue:work
- php artisan add:usd
