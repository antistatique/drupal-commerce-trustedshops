<?php

namespace Drupal\Tests\commerce_trustedshops\Functional;

use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\commerce_order\Functional\OrderBrowserTestBase;
use Drupal\commerce_trustedshops\Entity\Shop;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Tests the action to invite a custom to review an order.
 *
 * @group commerce_trustedshops
 */
class InviteReviewMultilanguageTest extends OrderBrowserTestBase {

  /**
   * The shop entity.
   *
   * @var \Drupal\commerce_trustedshops\Entity\ShopInterface
   */
  protected $shop;

  /**
   * The test order.
   *
   * @var \Drupal\commerce_order\Entity\OrderInterface
   */
  protected $order;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'locale',
    'language',
    'commerce_trustedshops',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Add a second language.
    ConfigurableLanguage::create([
      'id' => 'fr',
      'label' => 'French',
    ])->save();

    // Add a third language.
    ConfigurableLanguage::create([
      'id' => 'de',
      'label' => 'German',
    ])->save();

    // Add a langcode field on the shop entity.
    $field_storage = FieldStorageConfig::create([
      'field_name'  => 'langcode',
      'entity_type' => 'commerce_order',
      'type'        => 'text',
    ]);
    $field_storage->save();
    $instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle'        => 'default',
      'label'         => $this->randomMachineName(),
    ]);
    $instance->save();

    // Setup dummy TrustedShops Credentials API.
    $config = \Drupal::configFactory()->getEditable('commerce_trustedshops.settings');
    $config->set('api.username', 'john.doe@example.org');
    $config->set('api.password', 'qwertz');
    $config->save();

    $this->shop = Shop::create([
      'tsid' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store' => $this->store,
    ]);
    $this->shop->save();

    $order_item = $this->createEntity('commerce_order_item', [
      'type' => 'default',
      'unit_price' => [
        'number' => '999',
        'currency_code' => 'USD',
      ],
    ]);
    $this->order = $this->createEntity('commerce_order', [
      'type' => 'default',
      'mail' => $this->loggedInUser->getEmail(),
      'order_items' => [$order_item],
      'uid' => $this->loggedInUser,
      'store_id' => $this->store,
    ]);
    $this->order->save();

    $this->store->set('name', 'My Store');
    $this->store->save();
  }

  /**
   * {@inheritdoc}
   */
  protected function getAdministratorPermissions() {
    return array_merge([
      'administer commerce trustedshops',
      'send invite review commerce trustedshops',
    ], parent::getAdministratorPermissions());
  }


  /**
   * Tests warning messages when accessing the invite-to-review form
   * on an Order with a language which has no TrustedShop ID in the same lang.
   */
  public function testInviteReviewFormUnconfiguredShopLanguage() {
    // Setup a language for the TrustedShops ID.
    $this->shop->set('langcode', 'fr');
    $this->shop->save();

    $this->order->set('langcode', 'de');
    $this->order->save();

    $this->drupalGet('/admin/commerce/orders/' . $this->order->id() . '/trustedshops/invite_review_confirm');
    $this->assertSession()->statusCodeEquals(403);
    $this->assertSession()->pageTextContains('Please create a TrustedShop ID for the store My Store in German before inviting customer to review an order.');
  }

  /**
   * Tests inviting a customer to review an order.
   */
  public function testInviteReview() {
    // Setup a language for the TrustedShops ID.
    $this->shop->set('langcode', 'fr');
    $this->shop->save();

    $this->order->set('langcode', 'fr');
    $this->order->save();

    $this->drupalGet('/admin/commerce/orders/' . $this->order->id() . '/trustedshops/invite_review_confirm');
    $this->assertSession()->statusCodeEquals(200);

    // Check the integrity of the form.
    $this->assertSession()->optionExists('email_template', 'BEST_PRACTICE');
    $this->assertSession()->optionExists('email_template', 'CREATING_TRUST');
    $this->assertSession()->optionExists('email_template', 'CUSTOMER_SERVICE');

    $this->assertSession()->fieldDisabled('tsid');
    $this->assertSession()->fieldValueEquals('tsid', 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U');
    $this->assertSession()->fieldDisabled('language');
    $this->assertSession()->fieldValueEquals('language', 'French (fr)');
  }

}
