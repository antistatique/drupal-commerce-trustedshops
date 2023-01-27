<?php

namespace Drupal\commerce_trustedshops\Resolver\Shop;

use Drupal\commerce_store\CurrentStoreInterface;
use Drupal\commerce_trustedshops\Context;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Returns the site's shop based on the current store and the current lang.
 */
class DefaultShopResolver implements ShopResolverInterface {

  /**
   * The shop storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  protected $shopStorage;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The current store.
   *
   * @var \Drupal\commerce_store\CurrentStoreInterface
   */
  protected $currentStore;

  /**
   * The current language.
   *
   * @var \Drupal\Core\Language\LanguageInterface
   */
  protected $currentLang;

  /**
   * Constructs a new DefaultShopResolver object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\commerce_store\CurrentStoreInterface $current_store
   *   The current store.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository, CurrentStoreInterface $current_store, LanguageManagerInterface $language_manager) {
    $this->shopStorage = $entity_type_manager->getStorage('commerce_trustedshops_shop');

    $this->entityRepository = $entity_repository;
    $this->currentStore = $current_store;
    $this->currentLang = $language_manager->getCurrentLanguage();
  }

  /**
   * {@inheritdoc}
   */
  public function resolve(Context $context = NULL) {
    $query = $this->shopStorage
      ->getQuery()
      ->accessCheck(TRUE);

    /** @var \Drupal\commerce_store\Entity\StoreInterface $store */
    $store = $this->currentStore->getStore();
    if ($context && $context->getStore()) {
      $store = $context->getStore();
    }

    /** @var \Drupal\Core\Language\LanguageInterface $lang */
    $lang = $this->currentLang;
    if ($context && $context->getLanguage()) {
      $lang = $context->getLanguage();
    }

    $query->condition('langcode', $lang->getId())
      ->condition('store', $store->id())
      ->range(0, 1);
    $sids = $query->execute();

    if (!$sids) {
      return NULL;
    }

    $sid = reset($sids);
    $shop = $this->shopStorage->load($sid);
    return $this->entityRepository->getTranslationFromContext($shop, $lang->getId());
  }

}
