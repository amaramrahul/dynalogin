Roundcube dynalogin plugin
==========================

Introduction
------------

This plugin adds an additional input box to the login form where the user has to additionally enter the dynalogin OTP code in order to successfully authenticate.

This plugin has been tested with Roundcubve version 0.7 and 0.8. Dynalogin server had been setup in Debian Wheezy and Android dynalogin client was used for generating the OTP code.


Installation
------------

* Dynalogin server should be setup and configured (instructions for doing this are beyond the scope of this README)
* Download the dynalogin plugin to roundcube plugins directory.  
```
$ git clone https://github.com/amaramrahul/dynalogin.git
```
* Activate the plugin by adding it in roundcube config file (config/main.inc.php).  
```
$rcmail_config['plugins'] = array('dynalogin');
```

Configuration
-------------

If different from the defaults, the dynalogin server and port can be configured by renaming config.inc.php.dist to config.inc.php and modifying it.


LICENCE
-------

GPLv3


Author
------

Rahul Amaram (amaramrahul@users.sourceforge.net)

