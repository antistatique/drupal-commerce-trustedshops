# Commerce TrustedShops

|       Tests-CI        |        Style-CI         |        Downloads        |         Releases         |
|:----------------------:|:-----------------------:|:-----------------------:|:------------------------:|
| [![Build Status](https://github.com/antistatique/drupal-commerce-trustedshops/actions/workflows/ci.yml/badge.svg)](https://github.com/antistatique/drupal-commerce-trustedshops/actions/workflows/ci.yml) | [![Code styles](https://github.com/antistatique/drupal-commerce-trustedshops/actions/workflows/styles.yml/badge.svg)](https://github.com/antistatique/drupal-commerce-trustedshops/actions/workflows/styles.yml) | [![Downloads](https://img.shields.io/badge/downloads-8.x--1.0-green.svg?style=flat-square)](https://ftp.drupal.org/files/projects/commerce_trustedshops-8.x-1.0.tar.gz) | [![Latest Stable Version](https://img.shields.io/badge/release-v1.0-blue.svg?style=flat-square)](https://www.drupal.org/project/commerce_trustedshops/releases) |

## Features

Still under active development.

***Everything contained in this document is in draft form and subject to change at any time and provided for information purposes only***

- Display Trustedbadge
- Customize the Trustebadge
- Collect Reviews on orders
- *soon* Display Shop Review Sticker
- *soon* Display the Review Collector on products
- Send Invites Reviews from existing orders
- *soon* Send Automatic Invites Reviews according to Commerce Order State

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

The version `8.x-1.x` is not compatible with Drupal `8.8.x`.
Drupal `8.8.x` brings some breaking change with tests and so you
must upgrade to `8.x-2.x` version of **Commerce Trustedshops**.

Potion is available for both Drupal 8 & Drupal 9 !

## Which version should I use?

|Drupal Core|Commerce TrustedShops|Drupal Commerce|
|:---------:|:-----|:--------------|
|8.7.x      |1.x   |2.8            |
|8.8.x      |2.x   |2.8            |
|9.x        |2.x   |2.20           |

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
