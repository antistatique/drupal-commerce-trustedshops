<?php

namespace Drupal\commerce_trustedshops\Entity;

use Drupal\commerce_store\Entity\StoreInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Defines the interface for TrustedShops-IDs.
 */
interface ShopInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Gets the store.
   *
   * @return \Drupal\commerce_store\Entity\StoreInterface
   *   The store.
   */
  public function getStore();

  /**
   * Sets the store.
   *
   * @param \Drupal\commerce_store\Entity\StoreInterface $store
   *   The store.
   *
   * @return $this
   */
  public function setStore(StoreInterface $store);

  /**
   * Gets the store ID.
   *
   * @return int
   *   The store ID.
   */
  public function getStoreId();

  /**
   * Sets the store ID.
   *
   * @param int $store_id
   *   The store ID.
   *
   * @return $this
   */
  public function setStoreId($store_id);

  /**
   * Gets the shipment creation timestamp.
   *
   * @return int
   *   The shipment creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the shipment creation timestamp.
   *
   * @param int $timestamp
   *   The shipment creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

}
