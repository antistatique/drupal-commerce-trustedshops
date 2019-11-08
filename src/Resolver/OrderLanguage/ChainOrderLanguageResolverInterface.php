<?php

namespace Drupal\commerce_trustedshops\Resolver\OrderLanguage;

/**
 * Runs the added resolvers one by one until one of them returns the order lang.
 *
 * Each resolver in the chain can be another chain, which is why this interface
 * extends the order language resolver one.
 */
interface ChainOrderLanguageResolverInterface extends OrderLanguageResolverInterface {

  /**
   * Adds a resolver.
   *
   * @param \Drupal\commerce_trustedshops\Resolver\OrderLanguage\OrderLanguageResolverInterface $resolver
   *   The resolver.
   */
  public function addResolver(OrderLanguageResolverInterface $resolver);

  /**
   * Gets all added resolvers.
   *
   * @return \Drupal\commerce_trustedshops\Resolver\OrderLanguage\OrderLanguageResolverInterface[]
   *   The resolvers.
   */
  public function getResolvers();

}
