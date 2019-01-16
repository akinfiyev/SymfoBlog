# SymfoBlog

My first project using Symfony framework

## Deployment
### Built With

* [Composer](https://getcomposer.org/) - Dependency Manager for PHP
* [yarn](https://yarnpkg.com/en/) - Dependency Manager for fron-end

### Deploy
A step by step series that tell you how to get a development env running

Clone project:

```
git clone https://github.com/sliceice/SymfoBlog.git
```

Go to project folder

```
cd SymfoBlog
```

Init composer

```
composer install
```

Set your database configurations in .env file

```
touch .env
echo 'DATABASE_URL=mysql://root:root@localhost:3306/blog' >> .env
```

Load migrations
```
bin/console d:m:m
```

Load fixtures
```
bin/console d:f:l
```

Install webpack-encore
```
yarn add @symfony/webpack-encore --dev
```
 
Build asserts
```
yarn encore dev
```

Start server
```
bin/console server:start
```

Enjoy!