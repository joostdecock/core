# fso
Freesewing development

## System Requirements
* PHP 7 (but PHP 5.6 will work, too)
* composer

## Installation

### composer
Taken from the composer installation page: https://getcomposer.org/download/
<br />(The composer changes from time to time, so always check on the link above for the most current install instructions, specially the hash value)
```
 php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
 php -r "if (hash_file('SHA384', 'composer-setup.php') === '55d6ead61b29c7bdee5cccfb50076874187bd9f21f65d8991d46ec5cc90518f447387fb9f76ebae1fbbacf329e583e30') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
 php composer-setup.php
 php -r "unlink('composer-setup.php');"
```

### fso
```
 git clone git@github.com:joostdecock/fso.git
 composer install
 composer dump-autoload -o
 copy htaccess to .htaccess
```

## First call
* Enter in your browser {domain}/docs/demo/

## Troubleshooting

### OSX

For apache. If you installed nginx yourself, you probably know what to do

#### /private/etc/apache2/httpd.conf (needs sudo to edit)
Uncomment:
```
 LoadModule php5_module libexec/apache2/libphp5.so
 LoadModule userdir_module libexec/apache2/mod_userdir.so
 Include /private/etc/apache2/extra/httpd-userdir.conf
```
Add a ServerName entry as appropriate:
```
 ServerName mymachine:80
```
#### /etc/apache2/extra/httpd-userdir.conf (needs sudo to exit)
Uncomment:
```
 Include /private/etc/apache2/users/*.conf
```
#### Create/edit /private/etc/apache2/users/USERNAME.conf
Add a Directory directive. For example:
```
 <Directory "/Users/USERNAME/Sites">
    Options Indexes MultiViews
    AllowOverride None
    Order allow,deny
    Allow from localhost
 </Directory>
```
'Sites' is the default, to avoid fiddling, add a symlink;
```
 ln -s `pwd`/docs/ ~/Sites
```
#### Restart apache
```
 sudo apachectl -k restart
```
 Navigate to http://127.0.0.1/~USERNAME/index.php
 
### Windows 10
#### Installing PHP
You will have to make a change to your registry. Open Regedit and navigate to `HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\W3SVC\Parameters`. Change the `MajorVersion` value from `0x0000000a (10)` to `0x00000008 (8)`. 
Go to  http://php.iis.net and click on the “Install PHP Now” button. Instead of installing PHP, this will install the Microsoft Web Platform Installer (WPI) 5.0. In the search box at the top right corner, type in PHP and select PHP 7.09 and PHP Manager. After the install is complete, go back to the registry editor and change the value back to 10.
#### PHP.ini
Comment out the following lines in `php.ini`:

    extension=php_mysql.dll
    extension=php_wincache.dll

#### Installing Composer
Use an elevated command prompt to install Composer
