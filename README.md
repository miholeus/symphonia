Soulex Project
==============

CMS system based on popular Framework. Symfony Framework 2.x used.

Current Progress: In development

Features:

1. Build pages
2. Custom nodes on pages
3. Integrated menu
4. Admin panel in Joomla style


[![Build Status](https://travis-ci.org/miholeus/soulex.svg?branch=master)](https://travis-ci.org/miholeus/soulex)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/miholeus/soulex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/miholeus/soulex/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/miholeus/soulex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/miholeus/soulex/build-status/master)
[![codecov.io](https://codecov.io/github/miholeus/soulex/coverage.svg?branch=master)](https://codecov.io/github/miholeus/soulex?branch=master)

## Installation

[PHP](https://php.net) 5.3+ ,a database server, and [Composer](https://getcomposer.org) are required.

1. There are 2 ways of grabbing the code:
  * Use GitHub: simply download the zip on the right of the readme
  * Use Git: `git clone git@github.com:miholeus/soulex.git`
2. From a command line open in the folder, run `composer install`.
3. Enter your database details into `application/configs/application.ini`.

## Setting Up Caching

- mkdir cache

## Setting Up Logging

- mkdir logs

## Setting Up Themes

CMS ships with 1 customized theme. You can set your own scheme in public/skins/frontend/skin.xml file.