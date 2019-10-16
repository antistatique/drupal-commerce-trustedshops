<?php

namespace Drupal\commerce_trustedshops\Resolver\OrderLanguage;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_trustedshops\Context;
use Drupal\Core\Language\Language;

/**
 * Provides the order language, taking it directly from the order entity.
 */
class DefaultOrderLanguageResolver implements OrderLanguageResolverInterface {

  /**
   * {@inheritdoc}
   */
  public function resolve(OrderInterface $order, Context $context = NULL) {
    $field_name = NULL;
    if ($context && $context->hasData('field_name')) {
      $field_name = $context->getData('field_name', 'langcode');
    }

    // When the field exists and is not empty, return the value.
    if ($order->hasField($field_name) && !$order->get($field_name)->isEmpty()) {
      return new Language(['id' => $order->get($field_name)->value]);
    }

    // When the field is the default langcode value, use the entity getter.
    return $order->language();
  }

}
