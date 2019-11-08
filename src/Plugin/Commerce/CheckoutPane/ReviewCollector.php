<?php

namespace Drupal\commerce_trustedshops\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\commerce_trustedshops\Resolver\Shop\ChainShopResolverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Suggest the customer to leave a review after checkout pane.
 *
 * @CommerceCheckoutPane(
 *   id = "commerce_trustedshops_review_collector",
 *   label = @Translation("TrustedShop review collector after checkout"),
 *   default_step = "complete",
 * )
 */
class ReviewCollector extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * The chain resolver of Trusted Shop.
   *
   * @var \Drupal\commerce_trustedshops\Resolver\Shop\ChainShopResolverInterface
   */
  protected $chainShopResolver;

  /**
   * Constructs a new CollectReview object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface $checkout_flow
   *   The parent checkout flow.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_trustedshops\Resolver\Shop\ChainShopResolverInterface $chain_shop_resolver
   *   The chain resolver of Trusted Shop.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow, EntityTypeManagerInterface $entity_type_manager, ChainShopResolverInterface $chain_shop_resolver) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $checkout_flow, $entity_type_manager);
    $this->chainShopResolver = $chain_shop_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $checkout_flow,
      $container->get('entity_type.manager'),
      $container->get('commerce_trustedshops.chain_shop_resolver')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isVisible() {
    // This pane can only be shown at the end of checkout.
    if ($this->order->getState()->value == 'draft') {
      return FALSE;
    }

    // Can't collect review if no TrustedShops-ID have been configured.
    $shop = $this->chainShopResolver->resolve();
    if (!$shop) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $shop = $this->chainShopResolver->resolve();

    $pane_form['#theme'] = 'commerce_trustedshops_review_collector';
    $pane_form['#shop'] = $shop;
    $pane_form['#order'] = $this->order;

    return $pane_form;
  }

}
