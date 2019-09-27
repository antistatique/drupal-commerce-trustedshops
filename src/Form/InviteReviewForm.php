<?php

namespace Drupal\commerce_trustedshops\Form;

use Drupal\commerce_trustedshops\Context;
use Drupal\commerce_trustedshops\Resolver\ChainShopResolverInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\commerce_trustedshops\API\Review as TrustedShopsReview;

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
   * The Service to trigger invitations to review a shop.
   *
   * @var \Drupal\commerce_trustedshops\API\Review
   */
  protected $trustedShopsReview;

  /**
   * Constructs a new InviteReviewForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_trustedshops\Resolver\ChainShopResolverInterface $chain_shop_resolver
   *   The chain resolver of Trusted Shop.
   * @param \Drupal\commerce_trustedshops\API\Review $trustedshops_review
   *   The Service to trigger invitations to review a shop.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ChainShopResolverInterface $chain_shop_resolver, TrustedShopsReview $trustedshops_review) {
    $this->entityTypeManager = $entity_type_manager;
    $this->chainShopResolver = $chain_shop_resolver;
    $this->trustedShopsReview = $trustedshops_review;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('commerce_trustedshops.chain_shop_resolver'),
      $container->get('commerce_trustedshops.api.review')
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
      $this->messenger()->addWarning($this->t('Please configure your <a href="@settings-url" target="_blank"> TrustedShops credentials</a> before inviting customer to review an order.', [
        '@settings-url' => Url::fromRoute('commerce_trustedshops.settings')->toString(),
      ]));
      return AccessResult::forbidden();
    }

    // Confirms at least one TrustedShops-Id has been configured.
    $store = $commerce_order->getStore();
    $context = new Context($store);
    $shop = $this->chainShopResolver->resolve($context);
    if (!$shop) {
      $this->messenger()->addWarning($this->t('Please <a href="@crud-url" target="_blank">create a TrustedShop ID</a> for the store %store_name before inviting customer to review an order.', [
        '@crud-url' => Url::fromRoute('entity.commerce_trustedshops_shop.add_form', ['commerce_trustedshops_shop' => 1])->toString(),
        '%store_name' => $store->getName(),
      ]));
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

    $form['email_template'] = [
      '#type' => 'select',
      '#title' => $this->t('Email template'),
      '#options' => [
        'BEST_PRACTICE' => $this->t('Good practices', [], ['context' => 'commerce_trustedshops_email_template']),
        'CREATING_TRUST' => $this->t('Create more trust', [], ['context' => 'commerce_trustedshops_email_template']),
        'CUSTOMER_SERVICE' => $this->t('Service', [], ['context' => 'commerce_trustedshops_email_template']),
      ],
      '#default_value' => 'CUSTOMER_SERVICE',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the TrustedShops-ID configured for the given $store.
    $store = $this->order->getStore();
    $context = new Context($store);
    $shop = $this->chainShopResolver->resolve($context);

    $email_template = Xss::filter($form_state->getValue('email_template'), []);

    try {
      /** @var \Antistatique\TrustedShops\TrustedShops $ts */
      $ts = $this->trustedShopsReview->triggerShopReview($email_template, $this->order, $shop);
      $result = $ts->getLastResponse();

      // Get the response data.
      $data = $result['data']['reviewCollectorRequest']['reviewCollectorReviewRequests'][0];

      // TrustedShops may return a code 200 but still having errors.
      // We manage to show them in the UI here.
      if (isset($data['status']) && $data['status'] === 'ERROR') {
        $this->messenger()->addError($this->t('Something went wrong while triggering an invitation to write a review - via TrustedShops - for your order #%order_number.', [
          '%order_number' => $this->order->getOrderNumber(),
        ]));

        foreach ($data['errorMessages'] as $error) {
          $this->messenger()->addError($error['message']);
        }

        return;
      }

      $this->messenger()->addStatus($this->t('An invitation to review the order #%order_number has been sent to %customer_email.', [
        '%customer_email' => $this->order->getEmail(),
        '%order_number' => $this->order->getOrderNumber(),
      ]));
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Something went wrong while triggering an invitation to write a review - via TrustedShops - for your order #%order_number.<br>"@error".', [
        '@error' => $ts->getLastError(),
        '%order_number' => $this->order->getOrderNumber(),
      ]));
    }

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
