<?php

namespace Drupal\commerce_trustedshops\Resolver\Shop;

use Drupal\commerce_trustedshops\Context;

/**
 * Defines the interface for shop resolvers.
 */
interface ShopResolverInterface {

  /**
   * Resolves the shop.
   *
   * @param \Drupal\commerce_trustedshops\Context $context
   *   The context.
   *
   * @return Drupal\commerce_trustedshops\Entity\ShopInterface|null
   *   The shop, if resolved. Otherwise NULL, indicating that the next
   *   resolver in the chain should be called.
   */
  public function resolve(Context $context = NULL);

}
