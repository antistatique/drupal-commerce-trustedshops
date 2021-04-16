<?php

namespace Drupal\Tests\commerce_trustedshops\Unit\Event;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\commerce_trustedshops\Event\AlterProductDataEvent;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\commerce_trustedshops\Event\AlterProductDataEvent
 *
 * @group commerce_trustedshops
 */
class AlterProductDataEventTest extends UnitTestCase {

  /**
   * @covers ::getProductData
   * @covers ::setProductData
   * @covers ::getOrderItem
   */
  public function testEvent() {
    /** @var \Drupal\commerce_order\Entity\OrderItemInterface $orderItem */
    $orderItem = $this->prophesize(OrderItemInterface::class)->reveal();

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
