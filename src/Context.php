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
   * Constructs a new Context object.
   *
   * @param \Drupal\commerce_store\Entity\StoreInterface $store
   *   The store.
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language.
   */
  public function __construct(StoreInterface $store = NULL, LanguageInterface $language = NULL) {
    $this->store = $store;
    $this->language = $language;
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

}
