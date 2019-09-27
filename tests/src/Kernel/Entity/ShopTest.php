<?php

namespace Drupal\Tests\commerce_trustedshops\Kernel\Entity;

use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;
use Drupal\commerce_trustedshops\Entity\Shop;

/**
 * Tests the Shop entity.
 *
 * @coversDefaultClass \Drupal\commerce_trustedshops\Entity\Shop
 *
 * @group commerce_trustedshops
 */
class ShopTest extends CommerceKernelTestBase {

  /**
   * A sample user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'commerce_trustedshops'
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('commerce_trustedshops_shop');
    $this->installConfig('commerce_trustedshops');

    $this->user = $this->createUser();
  }

  /**
   * Tests the order entity and its methods.
   *
   * @covers ::getTsid
   * @covers ::setTsid
   * @covers ::getStore
   * @covers ::setStore
   * @covers ::getStoreId
   * @covers ::setStoreId
   * @covers ::getStoreId
   * @covers ::getCreatedTime
   * @covers ::setCreatedTime
   */
  public function testOrder() {
    /** @var \Drupal\commerce_trustedshops\Entity\ShopInterface $shop */
    $shop = Shop::create();
    $shop->save();

    $shop->setTsid('RCGABMX17MMTAF9V97G9DZEAKG1EILO0U');
    $this->assertEquals('RCGABMX17MMTAF9V97G9DZEAKG1EILO0U', $shop->getTsid());

    $this->assertNull($shop->getStore());
    $shop->setStore($this->store);
    $this->assertEquals($this->store, $shop->getStore());
    $this->assertEquals($this->store->id(),   $shop->getStoreId());
    $shop->setStoreId(0);
    $this->assertEquals(NULL, $shop->getStore());
    $shop->setStoreId($this->store->id());
    $this->assertEquals($this->store, $shop->getStore());
    $this->assertEquals($this->store->id(), $shop->getStoreId());

    $shop->setCreatedTime(635879700);
    $this->assertEquals(635879700, $shop->getCreatedTime());
  }
}
