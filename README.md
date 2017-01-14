# fso
Freesewing is a rewrite of the code that runs the MakeMyPattern.com backend.

It is currently feauture-complete with that legacy code, and we're gearing up to our first public release.

## System Requirements
* PHP 5.6 (we recommend PHP 7)
* composer

## Installation

### composer
Install composer. For instructions, see the composer installation page: https://getcomposer.org/download/

### fso
```
 git clone git@github.com:joostdecock/fso.git
 composer install
 composer dump-autoload -o
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
