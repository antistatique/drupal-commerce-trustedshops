<?php

namespace Drupal\Tests\commerce_trustedshops\Unit\Event;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\commerce_trustedshops\Event\AlterProductDataEvent;
use Drupal\Tests\UnitTestCase;
use Prophecy\Prophet;

/**
 * @coversDefaultClass \Drupal\commerce_trustedshops\Event\AlterProductDataEvent
 *
 * @group commerce_trustedshops
 */
class AlterProductDataEventTest extends UnitTestCase {

  /**
   * The prophecy object.
   *
   * @var \Prophecy\Prophet
   */
  private $prophet;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->prophet = new Prophet();
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    parent::tearDown();

    $this->prophet->checkPredictions();
  }

  /**
   * @covers ::getProductData
   * @covers ::setProductData
   * @covers ::getOrderItem
   */
  public function testEvent() {
    /** @var \Drupal\commerce_order\Entity\OrderItemInterface $orderItem */
    $orderItem = $this->prophet->prophesize(OrderItemInterface::class)->reveal();

    $event = new AlterProductDataEvent([
      'name' => 'Initial product',
      'sku' => 'test-123',
    ], $orderItem);

    $this->assertEquals([
      'name' => 'Initial product',
      'sku' => 'test-123',
    ], $event->getProductData());

    $event->setProductData([
      'name' => 'Updated product',
    ]);

    $this->assertEquals([
      'name' => 'Updated product',
    ], $event->getProductData());

    $this->assertInstanceOf(OrderItemInterface::class, $event->getOrderItem());
  }

}
