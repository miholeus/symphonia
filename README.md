Symphonia Project
==============

CMS system based on popular Framework. Symfony Framework 3.x used.

Current Progress: In development

Features:

1. Build pages
2. Custom nodes on pages
3. Integrated menu


[![Build Status](https://travis-ci.org/miholeus/soulex.svg?branch=master)](https://travis-ci.org/miholeus/soulex)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/miholeus/soulex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/miholeus/soulex/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/miholeus/soulex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/miholeus/soulex/build-status/master)
[![codecov.io](https://codecov.io/github/miholeus/soulex/coverage.svg?branch=master)](https://codecov.io/github/miholeus/soulex?branch=master)

## Installation

[PHP](https://php.net) 7.1+ ,a database server, and [Composer](https://getcomposer.org) are required.

1. There are 2 ways of grabbing the code:
  * Use GitHub: simply download the zip on the right of the readme
  * Use Git: `git clone git@github.com:miholeus/symphonia.git`
2. From a command line open in the folder, run `docker-compose up -d`.
3. Go to container, run `composer install`.
4. Enter your config into `app/config/parameters.yml`.
5. Run migrations with `php app/console doctrine:migrations:migrate` command.