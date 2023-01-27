<?php

namespace Drupal\commerce_trustedshops\Event;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event to alter TrustedShops product data.
 *
 * Allows to alter the product data before it is sent to TrustedShops.
 */
class AlterProductDataEvent extends Event {

  /**
   * The product data.
   *
   * @var string[]
   */
  protected $productData;

  /**
   * The commerce order item entity.
   *
   * @var \Drupal\commerce_order\Entity\OrderItemInterface
   */
  protected $orderItem;

  /**
   * Constructs a AlterProductDataEvent object.
   *
   * @param string[] $product_data
   *   The product data.
   * @param \Drupal\commerce_order\Entity\OrderItemInterface $order_item
   *   The commerce order item entity.
   */
  public function __construct(array $product_data, OrderItemInterface $order_item) {
    $this->productData = $product_data;
    $this->orderItem = $order_item;
  }

  /**
   * Get product data.
   *
   * @return string[]
   *   The product data.
   */
  public function getProductData() {
    return $this->productData;
  }

  /**
   * Set product data.
   *
   * @param string[] $product_data
   *   The product data.
   */
  public function setProductData(array $product_data) {
    $this->productData = $product_data;
  }

  /**
   * Get order item.
   *
   * @return \Drupal\commerce_order\Entity\OrderItemInterface
   *   The commerce order item entity.
   */
  public function getOrderItem() {
    return $this->orderItem;
  }

}
