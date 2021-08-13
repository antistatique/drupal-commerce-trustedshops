<?php

namespace Drupal\Tests\commerce_trustedshops\Kernel;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\ProductVariationType;
use Drupal\commerce_trustedshops\Context;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\profile\Entity\Profile;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase as DrupalCommerceKernelTestBase;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\commerce_trustedshops\Traits\DeprecationSuppressionTrait;

/**
 * @coversDefaultClass \Drupal\commerce_trustedshops\Resolver\OrderLanguage\DefaultOrderLanguageResolver
 *
 * @group commerce_trustedshops
 */
class DefaultOrderLanguageResolverTest extends DrupalCommerceKernelTestBase {
  use DeprecationSuppressionTrait;

  /**
   * The Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Trusted Shops used for test.
   *
   * @var \Drupal\commerce_trustedshops\Entity\ShopInterface[]
   */
  protected $testShops;

  /**
   * The Stores used for test.
   *
   * @var \Drupal\commerce_store\Entity\StoreInterface[]
   */
  protected $testStores;

  /**
   * The Order used for test.
   *
   * @var \Drupal\commerce_order\Entity\OrderInterface
   */
  protected $testOrder;

  /**
   * The default Trusted Shop resolver.
   *
   * @var \Drupal\commerce_trustedshops\Resolver\OrderLanguage\DefaultOrderLanguageResolver
   */
  protected $defaultOrderLanguageResolver;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'locale',
    'language',
    'content_translation',
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
  protected function setUp(): void {
    parent::setUp();
    $this->setupMultilingual();

    $this->installEntitySchema('profile');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('commerce_product_variation');
    $this->installConfig(['commerce_product', 'commerce_order']);
    $this->installSchema('commerce_number_pattern', ['commerce_number_pattern_sequence']);

    $this->installEntitySchema('commerce_trustedshops_shop');
    $this->container->get('content_translation.manager')
      ->setEnabled('commerce_trustedshops_shop', 'tsid', TRUE);

    $field_storage = FieldStorageConfig::create([
      'field_name'  => 'field_language',
      'entity_type' => 'commerce_order',
      'type' => 'language',
      'cardinality' => 1,
    ]);
    $field_storage->save();
    $instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'default',
      'label'  => $this->randomMachineName(),
    ]);
    $instance->save();

    $this->defaultOrderLanguageResolver = $this->container->get('commerce_trustedshops.default_order_language_resolver');

    $this->testStores[] = $this->entityTypeManager->getStorage('commerce_store')->load(1);
    $store_2 = $this->entityTypeManager
      ->getStorage('commerce_store')->create([
        'type' => 'online',
        'name' => $this->randomString(8),
        'mail' => $this->randomString(16),
      ]);
    $store_2->save();
    $this->testStores[] = $store_2;

    $ts_1 = $this->entityTypeManager
      ->getStorage('commerce_trustedshops_shop')->create([
        'tsid' => 'R9H9J8ETR1PBFYZ1564ODXCII6EU9ZRNRFI8SC',
        'langcode' => 'en',
        'store' => 1,
      ]);
    $ts_1->addTranslation('fr', [
      'tsid' => 'Z8F8A8ETZ1PBFiZ1223ODXCPP6EU8ZRNRFP8SC',
    ]);
    $ts_1->save();
    $this->testShops[$ts_1->id()] = $ts_1;

    $ts_2 = $this->entityTypeManager
      ->getStorage('commerce_trustedshops_shop')->create([
        'tsid' => 'EO2EDIY26B5JB3T50ICP0XZM4BJ3SNSK73DZ6BO',
        'langcode' => 'fr',
        'store' => 2,
      ]);
    $ts_2->save();
    $this->testShops[$ts_2->id()] = $ts_2;

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

    $this->testOrder = Order::create([
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
      'field_language' => new Language(['id' => 'fr']),
    ]);
    $this->testOrder->save();
    $this->testOrder->recalculateTotalPrice();
  }

  /**
   * Sets up the multilingual items.
   */
  protected function setupMultilingual() {
    // Add a new language.
    ConfigurableLanguage::createFromLangcode('fr')->save();
  }

  /**
   * @covers \Drupal\commerce_trustedshops\Resolver\OrderLanguage\DefaultOrderLanguageResolver::resolve
   */
  public function testResolveOrderLanguage() {
    $language = $this->defaultOrderLanguageResolver->resolve($this->testOrder);
    $this->assertInstanceOf(LanguageInterface::class, $language);
    $this->assertEquals('und', $language->getId());
  }

  /**
   * @covers \Drupal\commerce_trustedshops\Resolver\OrderLanguage\DefaultOrderLanguageResolver::resolve
   */
  public function testResolveOrderUndefinedField() {
    $context = new Context(NULL, NULL, ['field_name' => 'field_langcode']);
    $language = $this->defaultOrderLanguageResolver->resolve($this->testOrder, $context);

    $this->assertInstanceOf(LanguageInterface::class, $language);
    $this->assertEquals('und', $language->getId());
  }

  /**
   * @covers \Drupal\commerce_trustedshops\Resolver\OrderLanguage\DefaultOrderLanguageResolver::resolve
   */
  public function testResolveOrderExistingField() {
    $context = new Context(NULL, NULL, ['field_name' => 'field_language']);
    $language = $this->defaultOrderLanguageResolver->resolve($this->testOrder, $context);

    $this->assertInstanceOf(LanguageInterface::class, $language);
    $this->assertEquals('fr', $language->getId());
  }

}
