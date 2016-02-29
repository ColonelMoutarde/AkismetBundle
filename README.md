AkismetBundle
-------------
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/243552ac-5d33-4e9c-b05a-186fc6f507ea/mini.png)](https://insight.sensiolabs.com/projects/243552ac-5d33-4e9c-b05a-186fc6f507ea)
[![Build Status](https://travis-ci.org/OpenClassrooms/AkismetBundle.svg)](https://travis-ci.org/OpenClassrooms/AkismetBundle)
[![Coverage Status](https://coveralls.io/repos/OpenClassrooms/AkismetBundle/badge.svg?branch=master)](https://coveralls.io/r/OpenClassrooms/AkismetBundle?branch=master)

The AkismetBundle offers integration of the Akismet Library.
Akismet Library is a PHP5 library that provides [Akismet Spam Protection service](https://akismet.com/) functionality in your application.
See [Akismet](https://github.com/OpenClassrooms/Akismet) for full details.

## Installation
This bundle can be installed using composer:

```composer require openclassrooms/akismet-bundle```

or by adding the package to the composer.json file directly:

```json
{
    "require": {
        "openclassrooms/akismet-bundle": "*"
    }
}
```

After the package has been installed, add the bundle to the AppKernel.php file:
```php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new OpenClassrooms\Bundle\AkismetBundle\OpenClassroomsAkismetBundle(),
    // ...
);
```

## Configuration
```yml
# app/config/config.yml

open_classrooms_akismet:
    key:  %akismet.key%
    blog: %akismet.blog%
```

## Usage
```php

$commentBuilder = $container->get('openclassrooms.akismet.models.comment_builder');
$akismet = $container->get('openclassrooms.akismet.services.akismet_service');

$comment = $commentBuilder->create()
                          ...
                          ->build();
               
if ($akismet->commentCheck($comment)) {
 // store the comment and mark it as spam (in case of a mis-diagnosis).
} else {
 // store the comment normally
}

// and

$akismet->submitSpam($comment);

// and

$akismet->submitHam($comment);
```
