<?php

namespace Drupal\Tests\commerce_trustedshops\Kernel;

use Drupal\commerce_trustedshops\Context;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase as DrupalCommerceKernelTestBase;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\commerce_trustedshops\Traits\DeprecationSuppressionTrait;

/**
 * @coversDefaultClass \Drupal\commerce_trustedshops\Resolver\Shop\DefaultShopResolver
 *
 * @group commerce_trustedshops
 */
class DefaultShopResolverTest extends DrupalCommerceKernelTestBase {
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
   * The stores used for test.
   *
   * @var \Drupal\commerce_store\Entity\StoreInterface[]
   */
  protected $testStores;

  /**
   * The default Trusted Shop resolver.
   *
   * @var \Drupal\commerce_trustedshops\Resolver\Shop\DefaultShopResolver
   */
  protected $defaultShopResolver;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'locale',
    'language',
    'content_translation',
    'commerce_store',
    'commerce_trustedshops',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('commerce_trustedshops_shop');
    $this->container->get('content_translation.manager')
      ->setEnabled('commerce_trustedshops_shop', 'tsid', TRUE);

    // Add French as second lang. English is always added by default.
    ConfigurableLanguage::createFromLangcode('fr')->save();

    $this->defaultShopResolver = $this->container->get('commerce_trustedshops.default_shop_resolver');

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
  }

  /**
   * @covers \Drupal\commerce_trustedshops\Resolver\Shop\DefaultShopResolver::resolve
   *
   * @dataProvider providerDefaultShopAndLanguage
   */
  public function testResolve($store_id, LanguageInterface $language, $expected_shop_id, $expected_tsid) {
    $store_storage = $this->entityTypeManager->getStorage('commerce_store');
    $shop_storage = $this->entityTypeManager->getStorage('commerce_trustedshops_shop');

    $store = $store_storage->load($store_id);
    $context = new Context($store, $language);

    $shop = $this->defaultShopResolver->resolve($context);
    $expected_shop = $shop_storage->load($expected_shop_id);

    $this->assertEqual($shop->id(), $expected_shop->id());
    $this->assertEqual($shop->get('tsid')->value, $expected_tsid);
  }

  /**
   * Every potential Store & Language variations.
   *
   * @return array
   *   An array of each store/language variation.
   */
  public function providerDefaultShopAndLanguage() {
    return [
      [1, new Language(['id' => 'en']), 1,
        'R9H9J8ETR1PBFYZ1564ODXCII6EU9ZRNRFI8SC',
      ],
      [1, new Language(['id' => 'fr']), 1,
        'Z8F8A8ETZ1PBFiZ1223ODXCPP6EU8ZRNRFP8SC',
      ],
      [2, new Language(['id' => 'fr']), 2,
        'EO2EDIY26B5JB3T50ICP0XZM4BJ3SNSK73DZ6BO',
      ],
    ];
  }

  /**
   * @covers \Drupal\commerce_trustedshops\Resolver\Shop\DefaultShopResolver::resolve
   */
  public function testResolveWhitoutContext() {
    $shop = $this->defaultShopResolver->resolve();
    $shop_storage = $this->entityTypeManager->getStorage('commerce_trustedshops_shop');
    $expected_shop = $shop_storage->load($this->testShops[1]->id());
    $this->assertEqual($expected_shop, $shop);
  }

  /**
   * @covers \Drupal\commerce_trustedshops\Resolver\Shop\DefaultShopResolver::resolve
   */
  public function testResolveNull() {
    $context = new Context($this->testStores[1], new Language(['id' => 'en']));
    $shop = $this->defaultShopResolver->resolve($context);
    $this->assertNull($shop);
  }

}
