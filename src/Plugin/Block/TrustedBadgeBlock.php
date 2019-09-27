<?php

namespace Drupal\commerce_trustedshops\Plugin\Block;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\commerce_trustedshops\Resolver\ChainShopResolverInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides block to implement the TrustedBadge.
 *
 * @Block(
 *   id = "commerce_trustedshops_trustedbadge_block",
 *   admin_label = @Translation("TrustedBadge"),
 *   category = @Translation("TrustedShops")
 * )
 */
class TrustedBadgeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The chain resolver of Trusted Shop.
   *
   * @var \Drupal\commerce_trustedshops\Resolver\ChainShopResolverInterface
   */
  protected $chainShopResolver;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ChainShopResolverInterface $chain_shop_resolver) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->chainShopResolver = $chain_shop_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('commerce_trustedshops.chain_shop_resolver')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    $shop = $this->chainShopResolver->resolve();
    if (!$shop) {
      return AccessResult::forbidden();
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $shop = $this->chainShopResolver->resolve();
    return [
      '#theme' => 'commerce_trustedshops_trustedbadge_block',
      '#tsid' => $shop->getTsid(),
      '#cache' => [
        'contexts' => ['url'],
      ],
    ];
  }

}
