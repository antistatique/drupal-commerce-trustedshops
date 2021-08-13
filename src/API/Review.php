<?php

namespace Drupal\commerce_trustedshops\API;

use Antistatique\TrustedShops\TrustedShops;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\commerce_trustedshops\Entity\ShopInterface;
use Drupal\commerce_trustedshops\Event\AlterProductDataEvent;
use Drupal\commerce_trustedshops\Event\TrustedShopsEvents;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Allow merchants to trigger review request emails to customers.
 *
 * These emails are triggered by the merchant, but sent by Trusted Shops.
 * The merchant can collect shop and product reviews via this API.
 */
class Review {

  /**
   * The TrustedShops API Wrapper.
   *
   * @var \Antistatique\TrustedShops\TrustedShops
   */
  protected $trustedShops;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Construct a new Review object.
   *
   * @param \Antistatique\TrustedShops\TrustedShops $trusted_shops
   *   The TrustedShops API Wrapper.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(TrustedShops $trusted_shops, ConfigFactoryInterface $config_factory, EventDispatcherInterface $event_dispatcher) {
    $this->trustedShops = $trusted_shops;
    $this->configFactory = $config_factory;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * Trigger an invitation to review an order to the order's customer.
   *
   * Attempts to trigger a review via the TrustedShops API. An exception may be
   * throw on error from the TrustedShops API.
   * The e-mail language will use the TSID configured language.
   *
   * @param string $email_template
   *   The email template to use.
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order to write a review for.
   * @param \Drupal\commerce_trustedshops\Entity\ShopInterface $shop
   *   The TrustedShops-ID to use for review.
   *
   * @return \Antistatique\TrustedShops\TrustedShops
   *   The TrustedShops object containing the response.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Exception
   */
  public function triggerShopReview($email_template, OrderInterface $order, ShopInterface $shop) {
    $config = $this->configFactory->get('commerce_trustedshops.settings');

    // Get configurations values for both optional API credentials.
    $username = $config->get('api.username');
    $password = $config->get('api.password');

    // Does the Test environment is enabled?
    $test_mode = $config->get('test_mode') === TRUE ? 'api-qa' : NULL;

    // @todo Improve Antistatique\TrustedShops\TrustedShops to allow toggle
    // enable/disable of api-qa without passing in constructor.
    $this->trustedShops->setEndpoint($test_mode, 'restricted');
    $this->trustedShops->setApiCredentials($username, $password);

    $trusted_products = [];
    foreach ($order->getItems() as $order_item) {
      $trusted_product = [
        'name' => $order_item->getTitle(),
      ];

      $purchased_entity = $order_item->getPurchasedEntity();

      if ($purchased_entity instanceof ProductVariationInterface) {
        $product = $purchased_entity->getProduct();

        $trusted_product['sku'] = $purchased_entity->getSku();
        $trusted_product['url'] = $product
          ? $product->toUrl('canonical', ['absolute' => TRUE])->toString()
          : $purchased_entity->toUrl('canonical', ['absolute' => TRUE])->toString();
      }

      // Open product data to alterations.
      $event = new AlterProductDataEvent($trusted_product, $order_item);
      $this->eventDispatcher->dispatch(TrustedShopsEvents::ALTER_PRODUCT_DATA, $event);

      $trusted_products[] = $event->getProductData();
    }

    /** @var \Drupal\address\AddressInterface $address */
    $address = $order->getBillingProfile()->get('address');

    $placed = DrupalDateTime::createFromTimestamp($order->getPlacedTime());
    $now = \DateTime::createFromFormat('U', time());
    $now->setTimezone(new \DateTimeZone('UTC'));

    // The triggered e-mail will use the TSID configured language.
    // TrustedShops does not support language overriding via any parameters.
    $this->trustedShops->post('shops/' . $shop->tsid->value . '/reviews/trigger.json', [
      'reviewCollectorRequest' => [
        'reviewCollectorReviewRequests' => [
          [
            'reminderDate' => $now->format('Y-m-d'),
            'template' => [
              'variant' => $email_template,
              'includeWidget' => 'true',
            ],
            'order' => [
              'orderDate' => $placed->format('Y-m-d'),
              // TrustedShops has a limitation of at least 2 chars for
              // orderReference. To bypass this limitation pass, we prefix
              // every reference with "order-".
              'orderReference' => 'order-' . $order->getOrderNumber(),
              'currency' => $order->getTotalPrice()->getCurrencyCode(),
              'estimatedDeliveryDate' => $now->format('Y-m-d'),
              'products' => $trusted_products,
            ],
            'consumer' => [
              'firstname' => $address->given_name,
              'lastname' => $address->family_name,
              'contact' => [
                'email' => $order->getEmail(),
              ],
            ],
          ],
        ],
      ],
    ]);

    return $this->trustedShops;
  }

}
