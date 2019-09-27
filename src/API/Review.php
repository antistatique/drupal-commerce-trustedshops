<?php

namespace Drupal\commerce_trustedshops\API;

use Antistatique\TrustedShops\TrustedShops;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_trustedshops\Entity\ShopInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;

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
   * Construct a new Review object.
   *
   * @param \Antistatique\TrustedShops\TrustedShops $trusted_shops
   *   The TrustedShops API Wrapper.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(TrustedShops $trusted_shops, ConfigFactoryInterface $config_factory) {
    $this->trustedShops = $trusted_shops;
    $this->configFactory = $config_factory;
  }

  /**
   * Trigger an invitation to review an order to the order's customer.
   *
   * Attempts to trigger a review via the TrustedShops API. An exception may be
   * throw on error from the TrustedShops API.
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

    // @todo: Improve Antistatique\TrustedShops\TrustedShops to allow toggle
    // enable/disable of api-qa without passing in constructor.
    $this->trustedShops->setEndpoint($test_mode, 'restricted');
    $this->trustedShops->setApiCredentials($username, $password);

    $trusted_products = [];
    foreach ($order->getItems() as $order_item) {
      /** @var \Drupal\commerce_product\Entity\ProductVariationInterface $purchased_entity */
      $purchased_entity = $order_item->getPurchasedEntity();
      /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
      $product = $purchased_entity->getProduct();

      $trusted_products[] = [
        'sku'  => $purchased_entity->sku->value,
        'name' => $purchased_entity->getTitle(),
        'url'  => $product->toUrl('canonical', ['absolute' => TRUE])
          ->toString(),
      ];
    }

    /** @var \Drupal\address\AddressInterface $address */
    $address = $order->getBillingProfile()->get('address');

    $placed = DrupalDateTime::createFromTimestamp($order->getPlacedTime());
    $now = \DateTime::createFromFormat('U', time());
    $now->setTimezone(new \DateTimeZone('UTC'));

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
