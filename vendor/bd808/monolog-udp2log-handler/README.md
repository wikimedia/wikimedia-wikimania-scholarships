udp2log-monolog-handler
=======================

[Monolog][] handler that replicates the behavior of the [MediaWiki][]
wfErrorLog() logging system. Log output can be directed to a local file, a PHP
stream, or a udp2log server.

[![Build Status][ci-status]][ci-home]


Installation
------------
udp2log-monolog-handler is available on Packagist
([bd808/udp2log-monolog-handler][]) and is installable via [Composer][].

    {
      "require": {
        "bd808/udp2log-monolog-handler": "dev-master"
      }
    }

If you do not use Composer, you can get the source from GitHub and use any
PSR-4 compatible autoloader.

    $ git clone https://github.com/bd808/udp2log-monolog-handler.git


Run the tests
-------------
Tests are automatically performed by [Travis CI][]:
[![Build Status][ci-status]][ci-home]

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install --dev
    phpunit

---
[Monolog]: https://github.com/Seldaek/monolog
[MediaWiki]: https://www.mediawiki.org/wiki/MediaWiki
[ci-status]: https://travis-ci.org/bd808/udp2log-monolog-handler.png
[ci-home]: https://travis-ci.org/bd808/udp2log-monolog-handler
[bd808/udp2log-monolog-handler]: https://packagist.org/packages/bd808/udp2log-monolog-handler
[Composer]: https://getcomposer.org
[Travis CI]: https://travis-ci.org
