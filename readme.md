

## About Laravel Dusk Spider

Laravel Dusk Spider is a very simple tool to crawl the webpages of any website through dusk package that is provided by Laravel.


You can go through this medium article to see how to get started and run this tool.

https://medium.com/@tushargugnani_54389/crawling-website-using-laravel-dusk-spider-bbbbe487a21


Steps to Run the Tool on your Local.

1. Download / Clone the git repo.
2. Run composer update
3. Add `.env` file and connect your project to new database.
4. Run command `php artisan migrate`
5. Open file `Tests/Browser/duskSpiderTest.php` and change variable `$domain` and `$startUrl` to website url you want to crawl
6. Run command `php artisan dusk` to start crawling via dusk test.
7. Crawled results will be saved in pages table in your database.


