
<!-- ABOUT THE PROJECT -->
## About The Project
Symfony Case


<!-- GETTING STARTED -->
## Getting Started
### Prerequisites

This is  you need to use the softwares
* postresql
* openssl
* php
* composer

### Installation

```sh
git clone https://github.com/yasincetintas/next4bizCase.git
```
3. Install Symfony packages
```sh
php bin/console composer install
```
4. Crate Scheme and Tables
```sh
php bin/console doctrine:schema:update --force
```

5. Run SQL command on Database App

   [Database](https://drive.google.com/drive/folders/1zEOHb7EkXZOEFr1a-alKNyxgjOWsDmlc?usp=sharing)


6. Login
```sh
curl --location --request POST 'http://127.0.0.1:8000/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "username": "yasin",
    "password": "Aa123"
}'
```

7. Get Date

Last step copy Token field and paste after --header 'Authorization: Bearer
```sh
curl --location --request POST 'http://127.0.0.1:8000/api/test/itemCount' \
--header 'Authorization: Bearer COPIED TOKEN VALUE' \
--header 'Content-Type: application/json' \
--data-raw '{
    "period": "weekly",
    "date_range": {
        "start": "2021-01-22 00:00:00",
        "end": "2021-01-28 23:59:59"
    }
}'
```

<!-- USAGE EXAMPLES -->
## Usage

* Example Database Data and Table : [Database](https://drive.google.com/drive/folders/1zEOHb7EkXZOEFr1a-alKNyxgjOWsDmlc?usp=sharing)
* Postman Collection : [Postman Collection Path](https://github.com/yasincetintas/next4bizCase/blob/master/postman/Next4biz.postman_collection.json)
* How to install or Generate OpenSSL : [Documentation](https://emirkarsiyakali.com/implementing-jwt-authentication-to-your-api-platform-application-885f014d3358?source=social.tw )

<!-- CONTACT -->
## Contact

Yasin Çetintaş - [@Linkedin  Profile](https://www.linkedin.com/in/yasincetintas/) - ysnctnts@gmail.com

