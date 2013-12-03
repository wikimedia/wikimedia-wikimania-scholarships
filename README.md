Wikimania Scholarship Application
=================================

Collect information from scholarship applicants and provide a review
management workflow for processing the applications.


System Requirements
-------------------
* PHP >= 5.3.7


Setup
-----

### Sample Apache .htaccess file

```
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule .* index.php/$0 [L,PT]
</IfModule>
```

### Sample Nginx config

```
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
```

Configuration
-------------

The application follows the [Twelve-Factor App](http://12factor.net/)
configuration principle of configuration via environment variables. The
following variables are expected to be provided:

* DB_DSN = PDO DSN
* DB_USER = PDO username
* DB_PASS = PDO password
* APPLICATION_OPEN = Date/time that scholarship application period opens
* APPLICATION_CLOSE = Date/time that scholarship application period closes
* MOCK = Is this application is testing/development mode?

The following variables can be optionally provided:

* LOG_FILE = fopen()-compatible filename or stream URI (default: `php://stderr`)
* LOG_LEVEL = PSR-3 logging level (default: `notice`)

### Apache
````
SetEnv DB_DSN mysql:host=localhost;dbname=scholarships;charset=utf8
SetEnv DB_USER my_database_user
SetEnv DB_PASS "super secret password"
SetEnv APPLICATION_OPEN 2013-01-01T00:00:00Z
SetEnv APPLICATION_CLOSE 2013-02-01T00:00:00Z
SetEnv MOCK 1
````

### Nginx
````
env DB_DSN=mysql:host=localhost;dbname=scholarships;charset=utf8
env DB_USER=my_database_user
env DB_PASS="super secret password"
env APPLICATION_OPEN=2013-01-01T00:00:00Z
env APPLICATION_CLOSE=2013-02-01T00:00:00Z
env MOCK=1
````

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
