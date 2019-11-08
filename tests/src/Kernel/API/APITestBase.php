<?php

namespace Drupal\Tests\commerce_trustedshops\Kernel\API;

use Drupal\commerce_trustedshops\Entity\Shop;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\ProductVariationType;
use Drupal\profile\Entity\Profile;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Provides a base class for Commerce kernel tests.
 */
abstract class APITestBase extends CommerceKernelTestBase {

  /**
   * The shop entity.
   *
   * @var \Drupal\commerce_trustedshops\Entity\ShopInterface
   */
  protected $shop;

  /**
   * The test order.
   *
   * @var \Drupal\commerce_order\Entity\OrderInterface
   */
  protected $order;

  /**
   * A Mock of the TrustedShops API Wrapper.
   *
   * @var \Antistatique\TrustedShops\TrustedShops
   */
  protected $trustedShops;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'entity_reference_revisions',
    'state_machine',
    'profile',
    'address',
    'datetime',
    'commerce',
    'commerce_product',
    'commerce_order',
    'commerce_number_pattern',
    'commerce_price',
    'commerce_store',
    'commerce_trustedshops',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('profile');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('commerce_product_variation');
    $this->installConfig(['commerce_product', 'commerce_order']);
    $this->installSchema('commerce_number_pattern', ['commerce_number_pattern_sequence']);

    $this->installEntitySchema('commerce_trustedshops_shop');
    $this->installConfig('commerce_trustedshops');

    // Turn off title generation to allow explicit values to be used.
    $variation_type = ProductVariationType::load('default');
    $variation_type->setGenerateTitle(FALSE);
    $variation_type->save();

    $variation = ProductVariation::create([
      'type' => 'default',
      'sku' => 'TEST_p7gwvl76',
      'status' => TRUE,
      'price' => new Price('2.00', 'USD'),
    ]);
    $variation->save();

    $product = Product::create([
      'type' => 'default',
      'title' => 'Default testing product',
      'variations' => [$variation],
    ]);
    $product->save();

    $profile = Profile::create([
      'type' => 'customer',
    ]);
    $profile->save();

    $order_item = OrderItem::create([
      'type' => 'default',
      'unit_price' => [
        'number' => '999',
        'currency_code' => 'USD',
      ],
      'purchased_entity' => $variation,
    ]);

    $this->order = Order::create([
      'type' => 'default',
      // 'state' => 'placed',
      'mail' => 'john.doe@example.org',
      'uid' => 0,
      'ip_address' => '127.0.0.1',
      'order_number' => '6',
      'billing_profile' => $profile,
      'store_id' => $this->store->id(),
      'order_items' => [$order_item],
      'placed' => 635879700,
    ]);
    $this->order->save();
    $this->order->recalculateTotalPrice();

    $this->shop = Shop::create([
      'tsid' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store' => $this->store,
    ]);
    $this->shop->save();

    $mock_builder = $this->getMockBuilder('Antistatique\TrustedShops\TrustedShops')
      ->disableOriginalConstructor();
    $this->trustedShops = $mock_builder->getMock();
  }

}
