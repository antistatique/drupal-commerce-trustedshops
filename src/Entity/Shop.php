<?php

namespace Drupal\commerce_trustedshops\Entity;

use Drupal\commerce_store\Entity\StoreInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the TrustedShops-IDs entity class.
 *
 * @ContentEntityType(
 *   id = "commerce_trustedshops_shop",
 *   label = @Translation("TrustedShops-IDs"),
 *   label_collection = @Translation("TrustedShops-IDs"),
 *   label_singular = @Translation("trustedshops-ids"),
 *   label_plural = @Translation("trustedshops-ids"),
 *   label_count = @PluralTranslation(
 *     singular = "@count trustedshops-ids",
 *     plural = "@count trustedshops-ids",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\commerce_trustedshops\Form\ShopForm",
 *       "edit" = "Drupal\commerce_trustedshops\Form\ShopForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler"
 *   },
 *   base_table = "commerce_trustedshops_shop",
 *   data_table = "commerce_trustedshops_shop_field_data",
 *   admin_permission = "administer commerce trustedshops",
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "tsid",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/trustedshops/{commerce_trustedshops_shop}",
 *     "collection" = "/admin/commerce/trustedshops/shops",
 *     "add-form" = "/admin/commerce/trustedshops/shops/add",
 *     "edit-form" = "/admin/commerce/trustedshops/shops/{commerce_trustedshops_shop}/edit",
 *     "delete-form" = "/admin/commerce/trustedshops/shops/{commerce_trustedshops_shop}/delete",
 *   },
 * )
 */
class Shop extends ContentEntityBase implements ShopInterface {
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getTsid() {
    return $this->get('tsid')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTsid($tsid) {
    $this->set('tsid', $tsid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStore() {
    $referenced_entities = $this->get('store')->referencedEntities();
    $referenced_entity = reset($referenced_entities);
    return $referenced_entity ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setStore(StoreInterface $store) {
    $this->set('store', $store);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStoreId() {
    return $this->get('store')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setStoreId($store_id) {
    $this->set('store', $store_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['tsid'] = BaseFieldDefinition::create('string')
      ->setLabel(t('TrustedShops ID'))
      ->setDescription(t('The TrustedShop ID.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'default_value' => '',
        'max_length' => 255,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ]);

    $fields['store'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Store'))
      ->setDescription(t('The store for which the TrustedShops-IDs is attached to.'))
      ->setCardinality(1)
      ->setRequired(TRUE)
      ->setSetting('target_type', 'commerce_store')
      ->setSetting('handler', 'default')
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'commerce_entity_select',
        'weight' => 2,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time when the TrustedShops-ID was created.'))
      ->setTranslatable(TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time when the TrustedShops-ID was last edited.'))
      ->setTranslatable(TRUE);

    return $fields;
  }

}
