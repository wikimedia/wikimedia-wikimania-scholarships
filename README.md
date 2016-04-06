Wikimania Scholarships Application
=================================

Collect information from scholarship applicants and provide a review
management workflow for processing the applications.


System Requirements
-------------------
* PHP >= 5.3.7

Setup
-----

### Sample Apache .htaccess file

    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule .* index.php/$0 [L,PT]
    </IfModule>


### Sample Nginx config

    server {
        listen     80;
        server_name  scholarships;
        error_log  /opt/local/var/log/nginx/error.log debug;
        root /Library/WebServer/Documents/scholarships;

        location = / {
            try_files @site @site;
        }

        location / {
            try_files $uri @site;
        }

        location ~ \.php$ {
            index  index.php index.html index.htm;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            fastcgi_param  PHP_VALUE "error_log=/var/log/nginx/scholarships.log";
            include     fastcgi_params;
        }

        location @site {
            rewrite ^ /index.php;
        }
    }

Configuration
-------------

The application follows the [Twelve-Factor App](http://12factor.net/)
configuration principle of configuration via environment variables. The
following variables are expected to be provided:

* DB_DSN = PDO DSN
* DB_USER = PDO username
* DB_PASS = PDO password

The following variables can be optionally provided:

* MOCK = Is this application is testing/development mode? (default: `0`)
* LOG_FILE = fopen()-compatible filename or stream URI (default: `php://stderr`)
* LOG_LEVEL = PSR-3 logging level (default: `notice`)
* SMTP_HOST = SMTP mail server (default: `localhost`)
* CACHE_DIR = Directory to cache twig templates (default: `data/cache`)

### Apache

    SetEnv DB_DSN mysql:host=localhost;dbname=scholarships;charset=utf8
    SetEnv DB_USER my_database_user
    SetEnv DB_PASS "super secret password"
    SetEnv MOCK 1


### Nginx

    env DB_DSN=mysql:host=localhost;dbname=scholarships;charset=utf8
    env DB_USER=my_database_user
    env DB_PASS="super secret password"
    env MOCK=1

### .env file

For environments where container based configuration isn't possible or
desired, a `.env` file can be placed in the root of the project. This file
will be parsed using PHP's `parse_ini_file()` function and the resulting
settings will be injected into the application environment.

    DB_DSN="mysql:host=localhost;dbname=scholarships;charset=utf8"
    DB_USER=my_database_user
    DB_PASS="super secret password"
    MOCK=1


Hacking
-------

We manage PHP dependencies using Composer. This git repository includes the
Composer managed resources that are needed for deployment on the Wikimedia
Foundation production servers.

For local testing you will need to install several additional development-only
libraries:

    composer install

Once the testing libraries are installed you can run tests with this command:

    composer test

When submitting a patch for review you must ensure that your locally installed
testing libraries have been removed:

    composer install --no-dev
    composer dump-autoload --no-dev

A typical git commit should not include any changes to `composer.lock` or
files in the `vendor` directory. These files should only be updated when a new
runtime dependency is added or when the exact versions of the testing
libraries are updated.


Authors
-------
* Bryan Davis, Wikimedia Foundation
* Calvin W. F. Siu, Wikimania 2013 Hong Kong organizing team
* Katie Filbert, Wikimania 2012 Washington DC organizing team
* Harel Cain, Wikimania 2011 Haifa organizing team
* Wikimania 2010 Gdansk organizing team
* Wikimania 2009 Buenos Aires organizing team

### Translations
* [:pl:User:Saper](http://pl.wikipedia.org/wiki/User:Saper "Saper")
* [:pl:User:Magalia](http://pl.wikipedia.org/wiki/User:Magalia "Magalia")


License
-------
[GNU GPL 3.0](www.gnu.org/copyleft/gpl.html "GNU GPL 3.0")
