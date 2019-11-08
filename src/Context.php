<?php

namespace Drupal\commerce_trustedshops;

use Drupal\commerce_store\Entity\StoreInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Contains known global information (store, language).
 *
 * Passed to price resolvers and availability checkers.
 */
final class Context {

  /**
   * The store.
   *
   * @var \Drupal\commerce_store\Entity\StoreInterface
   */
  protected $store;

  /**
   * The language.
   *
   * @var \Drupal\Core\Language\LanguageInterface
   */
  protected $language;

  /**
   * The data.
   *
   * Used to provide additional information for a specific set of consumers
   * (e.g. price resolvers).
   *
   * @var array
   */
  protected $data;

  /**
   * Constructs a new Context object.
   *
   * @param \Drupal\commerce_store\Entity\StoreInterface $store
   *   The store.
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language.
   * @param array $data
   *   The data.
   */
  public function __construct(StoreInterface $store = NULL, LanguageInterface $language = NULL, array $data = []) {
    $this->store = $store;
    $this->language = $language;
    $this->data = $data;
  }

  /**
   * Gets the store.
   *
   * @return \Drupal\commerce_store\Entity\StoreInterface
   *   The store.
   */
  public function getStore() {
    return $this->store;
  }

  /**
   * Gets the language.
   *
   * @return \Drupal\Core\Language\LanguageInterface
   *   The language.
   */
  public function getLanguage() {
    return $this->language;
  }

  /**
   * Gets a data value with the given key.
   *
   * @param string $key
   *   The key.
   * @param mixed $default
   *   The default value.
   *
   * @return mixed
   *   The value.
   */
  public function getData($key, $default = NULL) {
    return $this->data[$key] ?? $default;
  }

  /**
   * Does the data key exists in the data collection.
   *
   * @param string $key
   *   The key.
   *
   * @return bool
   *   True when exists, FALSE otherwise.
   */
  public function hasData($key) {
    return isset($this->data[$key]);
  }

}
