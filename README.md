# Commerce TrustedShops

|       Travis-CI        |        Style-CI         |        Downloads        |         Releases         |
|:----------------------:|:-----------------------:|:-----------------------:|:------------------------:|
| [![Travis](https://travis-ci.org/antistatique/drupal-commerce-trustedshops.svg?branch=8.x-1.x)](https://travis-ci.org/antistatique/drupal-commerce-trustedshops) | [![StyleCI](https://styleci.io/repos/85471768/shield)](https://styleci.io/repos/190755687) | [![Downloads](https://img.shields.io/badge/downloads-8.x--1.0-green.svg?style=flat-square)](https://ftp.drupal.org/files/projects/commerce_trustedshops-8.x-1.0.tar.gz) | [![Latest Stable Version](https://img.shields.io/badge/release-v1.0-blue.svg?style=flat-square)](https://www.drupal.org/project/commerce_trustedshops/releases) |

## Features

Still under active development.

***Everything contained in this document is in draft form and subject to change at any time and provided for information purposes only***

- *wip* Display Trustedbadge
- *wip* Customize the Trustebadge 
- *wip* Collect Reviews on orders
- *wip* Display Shop Review Sticker
- *wip* Display the Review Collector on
- Send Invites Reviews from existing orders
- *wip* Send Automatic Invites Reviews according to Commerce Order State

_Please note: If you have already integrated the Trustbadge® manually, please delete it before continuing._

## You need Commerce TrustedShops if

* You want to display the Trustbadge on all pages,
* You want to customize the Trustbadge,
* You want to use Trusted Shops Product Reviews and have booked an appropriate package,
* You want to display the shop Review Sticker,
* You want to customize the look and content of your Review Sticker,
* You want to have the possibility to send manual invites for orders that happened in the past,
* You want to automatically send Trusted Shops Product Reviews from which Drupal Commerce order state (for example order completed),

Commerce TrustedShops can do a lot more than that, but those are some of the obvious uses of this module.

## Configuration

TBD

## Versions

Commerce TrustedShops is only available for Drupal 8 !
The module is ready to be used in Drupal 8, there are no known issues.

This version should work with all Drupal 8 releases using Drush 9+,
and it is always recommended keeping Drupal core installations up to date.

## Dependencies

This module relies on [TrustedShops PHP SDK](https://github.com/antistatique/trustedshops-php-sdk).

* `TrustedShops PHP SDK` is an external PHP library to communicate with the TrustedShops API.

We assume, that you have installed `antistatique/trustedshops-php-sdk` using Composer.

## Supporting organizations

This project is sponsored by Antistatique. We are a Swiss Web Agency,
Visit us at [www.antistatique.net](https://www.antistatique.net) or
[Contact us](mailto:info@antistatique.net).

## Getting Started

We highly recommend you to install the module using `composer`.

  ```bash
  composer require drupal/commerce-trustedshops
  ```

You can also install it using the `drush` or `drupal console` cli.

  ```bash
  drush dl commerce-trustedshops
  ```

  ```bash
  drupal module:install commerce-trustedshops
  ```

## Configuration

Configure your TrustedShops Credentials - as required by TrustedShops for manual invites - by adding the following code in your `settings.php`

  ```php
  /**
   * TrustedShops user.
   *
   * @var string
  */
  $config['commerce_trustedshops.settings']['api']['username'] = 'john.doe';

  /**
   * TrustedShops password.
   *
   * @var string
  */
  $config['commerce_trustedshops.settings']['api']['password'] = 'qwertz';
  ```
