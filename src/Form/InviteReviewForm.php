<?php

namespace Drupal\commerce_trustedshops\Form;

use Drupal\commerce_trustedshops\Context;
use Drupal\commerce_trustedshops\Resolver\ChainShopResolverInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Antistatique\TrustedShops\TrustedShops;

/**
 * Provides the invite review confirmation form.
 *
 * @internal
 */
class InviteReviewForm extends ConfirmFormBase {

  /**
   * The current order to send invitation for review.
   *
   * @var \Drupal\commerce_order\Entity\OrderInterface
   */
  protected $order;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The chain resolver of Trusted Shop.
   *
   * @var \Drupal\commerce_trustedshops\Resolver\ChainShopResolverInterface
   */
  protected $chainShopResolver;

  /**
   * Constructs a new InviteReviewForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_trustedshops\Resolver\ChainShopResolverInterface $chain_shop_resolver
   *   The chain resolver of Trusted Shop.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ChainShopResolverInterface $chain_shop_resolver) {
    $this->entityTypeManager = $entity_type_manager;
    $this->chainShopResolver = $chain_shop_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('commerce_trustedshops.chain_shop_resolver')
    );
  }

  /**
   * Checks access.
   *
   * Confirms that the user has the 'send invite review commerce trustedshops'
   * permission, the TrustedShops API configurations has been filled, and
   * the Order to send invitation for has a configured Trusted Shops-IDs.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   * @param \Drupal\commerce_order\Entity\OrderInterface $commerce_order
   *   The order to send a review for.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function access(RouteMatchInterface $route_match, AccountInterface $account, OrderInterface $commerce_order = NULL) {
    // Confirms the user has the 'send invite review commerce trustedshops'.
    if (!$account->hasPermission('send invite review commerce trustedshops')) {
      return AccessResult::forbidden();
    }

    // Get TrustedShops config.
    $config = $this->config('commerce_trustedshops.settings');
    $config->get('api.username');
    $config->get('api.password');

    // Confirms the TrustedShops API configurations has been filled.
    if (!$config->get('api.username') || !$config->get('api.password')) {
      return AccessResult::forbidden();
    }

    // Confirms at least one TrustedShops-Id has been configured.
    $store = $commerce_order->getStore();
    $context = new Context($store);
    $shop = $this->chainShopResolver->resolve($context);
    if (!$shop) {
      return AccessResult::forbidden();
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_trustedshops_invite_review_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to invite %mail to write a review?', [
      '%mail' => $this->order->getEmail(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    $entity_type_id = $this->order->getEntityTypeId();
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);

    if ($entity_type->hasLinkTemplate('collection')) {
      return new Url('entity.' . $entity_type_id . '.collection');
    }
    else {
      return new Url('<front>');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Invite to write a review');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return implode(' ', [
      $this->t('Sending invite emails is the fastest way to get your first reviews.'),
      $this->t('Once you have confirmed the request, TrustedShops will send an invite to write a review.'),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, OrderInterface $commerce_order = NULL) {
    $this->order = $commerce_order;
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('commerce_trustedshops.settings');

    // Get configurations values for both optional API credentials.
    $username = $config->get('api.username');
    $password = $config->get('api.password');

    // Confirms at least one TrustedShops-Id has been configured.
    $store = $this->order->getStore();
    $context = new Context($store);
    $shop = $this->chainShopResolver->resolve($context);

    $ts = new TrustedShops('restricted');
    $ts->setApiCredentials($username, $password);

    $trusted_products = [];
    foreach ($this->order->getItems() as $order_item) {
      /** @var Drupal\commerce_product\Entity\ProductVariationInterface $product_variation */
      $product_variation = $this->entityTypeManager->getStorage('commerce_product_variation')->load($order_item->getPurchasedEntityId());
      /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
      $product = $product_variation->getProduct();

      $trusted_products[] = [
        'sku' => $product_variation->sku->value,
        'name' => $product_variation->getTitle(),
        'url' => $product->toUrl('canonical', ['absolute' => TRUE])->toString(),
      ];
    }

    /** @var \Drupal\address\AddressInterface $address */
    $address = $this->order->getBillingProfile()->get('address');


    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}