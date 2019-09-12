<?php

namespace Drupal\commerce_trustedshops\Resolver;

use Drupal\commerce_trustedshops\Context;

/**
 * Default implementation of the chain trustedshops-ids resolver.
 */
class ChainShopResolver implements ChainShopResolverInterface {

  /**
   * The resolvers.
   *
   * @var \Drupal\commerce_trustedshops\Resolver\ShopResolverInterface[]
   */
  protected $resolvers = [];

  /**
   * Constructs a new ChainShopResolver object.
   *
   * @param \Drupal\commerce_trustedshops\Resolver\ShopResolverInterface[] $resolvers
   *   The resolvers.
   */
  public function __construct(array $resolvers = []) {
    $this->resolvers = $resolvers;
  }

  /**
   * {@inheritdoc}
   */
  public function addResolver(ShopResolverInterface $resolver) {
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
   */
  public function resolve(Context $context = NULL) {
    foreach ($this->resolvers as $resolver) {
      $result = $resolver->resolve($context);
      if ($result) {
        return $result;
      }
    }
  }

}
