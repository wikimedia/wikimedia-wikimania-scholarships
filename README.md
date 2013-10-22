##Setup

### Sample .htaccess file

```
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule .* index.php/$0 [PT]
</IfModule>
```

### Sample nginx config

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
      fastcgi_param  PHP_VALUE "error_log=/opt/local/var/log/nginx/scholarships.log";
      include     fastcgi_params;
    }

    location @site {
      rewrite ^ /index.php;
    }
  }
}
```

### Config file

Create includes/config.php, based on config.sample.php, filling in database credentials and other settings.

### Base URL

If your URL to the scholarship system is
 http://foo.bar/scholarships/
then your base URL is "/scholarships/".

Remember to set the base URL correctly in .htaccess (RewriteBase) and includes/config.php ($BASEURL).

## Authors
* Calvin W. F. Siu, Wikimania 2013 Hong Kong organizing team
* Katie Filbert, Wikimania 2012 Washington DC organizing team
* Harel Cain, Wikimania 2011 Haifa organizing team
* Wikimania 2010 Gdansk organizing team
* Wikimania 2009 Buenos Aires organizing team

###Translations
* [:pl:User:Saper](http://pl.wikipedia.org/wiki/User:Saper "Saper")
* [:pl:User:Magalia](http://pl.wikipedia.org/wiki/User:Magalia "Magalia")

##License
[GNU GPL 3.0](www.gnu.org/copyleft/gpl.html "GNU GPL 3.0")
