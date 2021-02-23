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

## The task in Russian

Есть S3 совместимое объектное хранилище на базе Minio. Ежедневно в 00:01 в хранилище выгружаются CSV-файлы с информацией о заказах за предыдущий день.
Пример содержимого CSV-файлов (orders_moscow_2020-10-28.csv, orders_novosibirsk_2020-10-28.csv):
ORDER_ID,TIMESTAMP,RUB
15012,1604781203,1246.20
15013,1604781412,1515.00
15014,1604781423,989.99
15015,1604781701,5121.21

Необходимо реализовать небольшой сервис, который ежедневно обходит новые CSV-файлы, создаёт в каждом таком файле столбец "USD" и сохраняет в него сконвертированную в доллары стоимость.
