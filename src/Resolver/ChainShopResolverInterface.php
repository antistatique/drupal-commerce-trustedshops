<?php

namespace Drupal\commerce_trustedshops\Resolver;

/**
 * Runs the added resolvers one by one until one of them returns the shop.
 *
 * Each resolver in the chain can be another chain, which is why this interface
 * extends the shop resolver one.
 */
interface ChainShopResolverInterface extends ShopResolverInterface {

  /**
   * Adds a resolver.
   *
   * @param \Drupal\commerce_trustedshops\Resolver\ShopResolverInterface $resolver
   *   The resolver.
   */
  public function addResolver(ShopResolverInterface $resolver);

  /**
   * Gets all added resolvers.
   *
   * @return \Drupal\commerce_trustedshops\Resolver\ShopResolverInterface[]
   *   The resolvers.
   */
  public function getResolvers();

}
