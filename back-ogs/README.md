## **Outils de Gestion de Sites - Be Com' Different**

### 1. Clone repository

### 2. Installation :

Back-End (Laravel 8) :

-   Change to directory :
    ```
    cd back
    ```
-   Install dependencies :
    ```
    composer install
    ```
-   Copy .env file :
    ```
    cp .env.example .env
    ```
-   Modify DB\_\* value in .env with your database config.
-   Modify MAIL\_\* values in .env (with your MailTrap config as an example to test).
-   Generate application key:
    ```
    php artisan key:generate
    ```
-   Migrate :
    ```
    php artisan migrate
    ```
-   Seed (create user credentials --> random hashed pwd to be reset for first login and websites list in DataBase) :
    ```
    php artisan db:seed
    ```
-   (Optional) Visualize and interact with the API’s resources via Swagger UI :
    ```
    php artisan l5-swagger:generate
    php artisan serve
    ```
    [=> OGS Swagger UI](http://127.0.0.1:8000/api/documentation)
-   Serve (the web server artisan is the PHP built-in web server, which is **not for use in any scenario other than development**) :

    ```
    php artisan serve

    ```

-   Starting the scheduler **locally** for Artisan command db::load (loading external datas and associated errors to database every 30 minutes) :

    ```
    php artisan schedule:work

    (On a server you'll need to add a single cron configuration entry that runs the schedule:run command every minute :
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1)
    ```
