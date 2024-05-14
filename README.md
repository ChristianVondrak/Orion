
## ORION: Payroll Management System for Avanty Way
## Description
**ORION** is a web application designed specifically for Avanty Way HR staff. Its main objective is to simplify and automate payroll management, allowing for a more efficient and accurate workflow. Below are the main features of ORION:

- **Access to Project Data:**
  Human resources users can access detailed information about ongoing projects. This includes details such as the project name, assigned team, start and end dates, and important milestones.


- **User Timing Tracking:**
  ORION records the work times of employees on different projects. Users can view individual and total timings for each employee, making it easy to assign salaries and resource planning.


- **Expected Salary Calculation:**
  The app automatically calculates the expected salary for each employee based on their time worked and the fees associated with each project. This ensures accurate and transparent compensation.


- **Generation of Invoices:**
  ORION allows you to automatically generate invoices for clients or projects. Users can customize invoice details such as: payment concepts, day-off and bonuses.

## Server Requirements

This project requires the basic elements of the [framework](https://laravel.com/docs/11.x/deployment), Listed as follows:

* PHP >= 8.2
* Ctype PHP Extension
* cURL PHP Extension
* DOM PHP Extension
* Fileinfo PHP Extension
* Filter PHP Extension
* Hash PHP Extension
* Mbstring PHP Extension
* OpenSSL PHP Extension
* PCRE PHP Extension
* PDO PHP Extension
* Session PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Node.js >= 18.19.0
* Npm >= 9.2.0

## Local Deployment
After cloning, we must apply the following steps from the root of the project directory:

1. Install the vendor dependencies of the framework.
    > `composer install`
2. Create the .env file from the example file.
    > `cp -a .env.example .env`
3. Generate the application key.
    > `php artisan key:generate`

## Docker Deployment

1. Run 
    > `docker compose up -d --build` to create and run the containers.
2. Enter the container where the server is running 
    > `docker exec -it <container_name or id> bash`
3. Continue from step 1. **Local Deployment**

## Migrations and Seeds

To facilitate the creation of initial tables necessary for the API, [migrations](https://laravel.com/docs/11.x/migrations) and [seeds](https://laravel.com/docs/11.x/seeding) have been created with the definition for the API auth.
<br>

### Migration

To execute the migrations, it should be enough to execute the following line from the root of the project.
```
php artisan migrate
```

If the migrations were executed correctly, the result in the prompt should be the following.

```
INFO  Running migrations.

0001_01_01_000000_create_users_table ...................................................................... 486.44ms DONE
0001_01_01_000001_create_cache_table ...................................................................... 174.49ms DONE
0001_01_01_000002_create_jobs_table ....................................................................... 382.27ms DONE
2024_04_23_181015_create_worksnap_users_table ............................................................. 117.54ms DONE
2024_04_23_181337_create_projects_table .................................................................... 64.13ms DONE
2024_04_23_181620_create_project_user_table ............................................................... 533.28ms DONE
2024_04_23_181826_create_timmings_table ................................................................... 467.48ms DONE
2024_04_23_182704_create_extra_fields_table ................................................................ 62.46ms DONE
2024_04_23_182832_create_operators_table .................................................................. 116.73ms DONE
2024_04_23_183049_create_reports_table .................................................................... 532.29ms DONE
2024_04_23_184225_create_extra_charges_table .............................................................. 566.41ms DONE
2024_04_30_202009_create_roles_table ....................................................................... 74.45ms DONE
2024_04_30_202400_add_roles_users_table ................................................................... 244.94ms DONE
2024_05_02_133548_add_two_factor_columns_to_users_table .................................................... 45.14ms DONE
2024_05_02_133611_create_personal_access_tokens_table ..................................................... 166.51ms DONE
```
### Seeds
To execute the seeds, it should be enough to execute the following line from the root of the project.
```
php artisan db:seed
```
- Mail: admin@admin.com
- Password: admin1234


## Asset Compilation

### Development
For a development environment and to be able to see changes in the code in real time we must use
```
npm run dev
```
If the assets were executed correctly, the result in the prompt should be the following.
```
> dev
> vite



  VITE v5.2.10  ready in 539 ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: http://172.17.0.2:5173/
  ➜  press h + enter to show help

  LARAVEL v11.4.0  plugin v1.0.2

  ➜  APP_URL: http://localhost
```
### Build
To prepare the application to be deployed in production with optimizations to improve performance.
```
npm run build
```
If the assets were executed correctly, the result in the prompt should be the following.

```
> build
> vite build

vite v5.2.10 building for production...
✓ 47 modules transformed.
public/build/manifest.json             0.27 kB │ gzip:  0.14 kB
public/build/assets/app-C4_ZgUET.css  56.89 kB │ gzip:  9.48 kB
public/build/assets/app-D2jpX1vH.js   29.83 kB │ gzip: 11.98 kB
✓ built in 2.91s

Process finished with exit code 0
```
