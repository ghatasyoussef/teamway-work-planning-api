# Overview
This project is a step for becoming a builder in teamway process. 

The "Work Planning Service" is a REST application built from scratch to serve as a work planning service. The primary focus of this application is to efficiently manage work shifts for workers based on specific business requirements. The application ensures that each worker has shifts, adhering to the defined rules:

- Shifts: A worker is assigned shifts, and each shift is precisely 8 hours long.
- No Same-Day Overlaps: A worker is guaranteed not to have two shifts on the same day, promoting a balanced and sustainable work schedule.
- 24-Hour Timetable: The work timetable follows a 24-hour cycle divided into three 8-hour shifts: 0-8, 8-16, 16-24.


# Repo Contents:
1. Code. 
3. E2E Tests.
3. Unit Tests.
2. Linting. 
2. Functional Documentation. 
2. API Documentation. 
2. Docker. 
3. Postman Collection.

# Main Stack

1. **Language**: PHP (v 8.2.x)
1. **Framework**: Laravel (version 8.x)
1. **Database**: MySQL

# Other Tools:
1. **Containerization**: Docker
1. **Functional Documentation**: PhpDocumentor
1. **API Documentation**: Scramble & redoc-cli
1. **Linting**: Duster
1. **Rest Client**: Postman



# Future Work:
- Move validation from controller to request class. 
- Move logic to services
- Make the scheduling problem automatically solved be modeling it as a constraint satisfaction algorithm.
- Make the docker production ready:
    1. Make the docker run on the final production output (using nginx) (for simplicity, currently it is being served using artisan). In addition, only make docker read relevant files (generated docs, the dockerfiles and so on should be in a parent folder for better structure)
    1. Run tests inside docker (possibly another image) to guarantee portability. 
    1. Persist the database, the public folder and other needed file. 


# Running the project:
The easiest way to run the project is to use Docker:
1. Clone the repo. 
1. Create .env file with appropriate values. 
2. Run docker-compose up. Behind the scene, the project will be initialized and database will be populated with data for easy usage. 

# Developing:
## Initiate the repo:
1. Clone the repo. 
1. Create .env file with appropriate values. 
1. Create an empty database with the name filled in .env. 


## Prepare Database:
### Migrations: 
Run migrations to construct the database. 
```bash
php artisan migrate
```

### Seeds (Optional): 
Run seeder if you want to populate the database. 
```bash
php artisan db:seed
```

Afterwards, start serving:
```bash
php artisan serve
```

## Linting: 
To check linting status:
```bash
./vendor/bin/duster lint
```
To fix linting problems:
```bash
./vendor/bin/duster fix
```

Linting is implemented using **Duster Linting**. [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)


## Functional Documentation:
To generate documents to *docs* folder:

```bash
phpdoc run -d app/ -t docs
```
Functional Documentation is implemented using **phpDocumentor**. [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)





## API Documentation:

API documentation was generated initially using **Scramble**:
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Afterwards **redoc-cli** was used to customize and polish the output. You can find the output OpenAPI JSON file and the decorated HTML file in "api-doc" folder. 

**redoc-cli**: [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)




## Testing:
1. Create ".env.testing" file and the environment variables to be used in testing; basically the database should be different.
2. To run tests, run the script file "run-test.sh". You can choose either generating the coverage report or just exectuing the tests by commenting and uncommenting the needed line(s).



## Debugging:

**Telescope** is integrated for easier future debugging (helpful when performance optimizations are needed, or insights are required).