<?php

namespace Drupal\commerce_trustedshops\Event;

/**
 * Defines events for the Commerce TrustedShops module.
 */
final class TrustedShopsEvents {

  /**
   * Allows to alter the product data before it is sent to TrustedShops.
   *
   * @Event
   *
   * @see \Drupal\commerce_trustedshops\Event\AlterProductDataEvent
   */
  const ALTER_PRODUCT_DATA = 'commerce_trustedshops.alter_product_data';

}
