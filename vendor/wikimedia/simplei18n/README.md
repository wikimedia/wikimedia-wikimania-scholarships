[![Build Status](https://travis-ci.org/wikimedia/simplei18n.svg?branch=master)](https://travis-ci.org/wikimedia/simplei18n) [![Latest Stable Version](https://poser.pugx.org/wikimedia/simplei18n/v/stable.svg)](https://packagist.org/packages/wikimedia/simplei18n)

Wikimedia Simplei18n
====================

No frills internationalization engine for use with PHP projects.

Implementation based on code from [MediaWiki][] and the
[Wikimania Scholarships application][].


Installation
------------
Wikimedia Simplei18n is available on Packagist
([wikimedia/simplei18n][]) and is installable via [Composer][].

    {
      "require": {
        "wikimedia/simplei18n": "dev-master"
      }
    }

If you do not use Composer, you can get the source from GitHub and use any
PSR-4 compatible autoloader.

    $ git clone https://github.com/wikimedia/simplei18n.git


Run the tests
-------------
Test are automatically performed by [Travis CI][].

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install --dev
    phpunit

---
[MediaWiki]: https://www.mediawiki.org/wiki/MediaWiki
[Wikimania Scholarships application]: https://www.mediawiki.org/wiki/Wikimania_Scholarships_app
[wikimedia/simplei18n]: https://packagist.org/packages/wikimedia/simplei18n
[Composer]: https://getcomposer.org
[Travis CI]: https://travis-ci.org
