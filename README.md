# Mage4 Cloudflare Turnstile

**Magento extension** to protect frontend and admin forms powered by CloudFlare Turnstile.

### Install via composer

```
composer require mage4/magento2-run-sql

php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

### Install Package manually by copy-paste

Download the code from this repo under Magento® 2 following directory:

```
app/code/Mage4/Turnstile
``` 
And run following commands to enable the module:
```
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## Features
The extension allows Turnstile to protect your Magento or Adobe Commerce following storefront and admin forms
- Custom Account Login.
- Customer Account Create.
- Customer Account Edit.
- Customer Account Forgot Password.
- Contact Us.
- Admin User Login.
- Admin User Forgot Password.

## About us
We’re an innovative development agency building awesome websites, webshops and web applications with Latest Technologies. Check out our website [mage4.com](http://mage4.com/) or [email](mailto:contact@mage4.com).

<img src="doc/mage4_logo.png">
