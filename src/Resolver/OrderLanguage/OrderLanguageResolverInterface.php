<?php

namespace Drupal\commerce_trustedshops\Resolver\OrderLanguage;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_trustedshops\Context;

/**
 * Defines the interface for order language resolvers.
 */
interface OrderLanguageResolverInterface {

  /**
   * Resolves the language from an order.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order.
   * @param \Drupal\commerce_trustedshops\Context $context
   *   The context.
   *
   * @return \Drupal\Core\Language\LanguageInterface|null
   *   The order language, if resolved. Otherwise NULL, indicating that the next
   *   resolver in the chain should be called.
   */
  public function resolve(OrderInterface $order, Context $context = NULL);

}
