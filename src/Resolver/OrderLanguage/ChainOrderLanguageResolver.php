<?php

namespace Drupal\commerce_trustedshops\Resolver\OrderLanguage;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_trustedshops\Context;

/**
 * Default implementation of the chain order language resolver.
 */
class ChainOrderLanguageResolver implements ChainOrderLanguageResolverInterface {

  /**
   * The resolvers.
   *
   * @var \Drupal\commerce_trustedshops\Resolver\OrderLanguage\OrderLanguageResolverInterface[]
   */
  protected $resolvers = [];

  /**
   * Constructs a new ChainOrderLanguageResolver object.
   *
   * @param \Drupal\commerce_trustedshops\Resolver\OrderLanguage\OrderLanguageResolverInterface[] $resolvers
   *   The resolvers.
   */
  public function __construct(array $resolvers = []) {
    $this->resolvers = $resolvers;
  }

  /**
   * {@inheritdoc}
   */
  public function addResolver(OrderLanguageResolverInterface $resolver) {
    $this->resolvers[] = $resolver;
  }

  /**
   * {@inheritdoc}
   */
  public function getResolvers() {
    return $this->resolvers;
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\Core\Language\LanguageInterface
   *   The order language, when not existing, the website default language.
   */
  public function resolve(OrderInterface $order, Context $context = NULL) {
    foreach ($this->resolvers as $resolver) {
      $result = $resolver->resolve($order, $context);
      if ($result) {
        return $result;
      }
    }
  }

}
