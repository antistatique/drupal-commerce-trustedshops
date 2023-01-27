<?php

namespace Drupal\Tests\commerce_trustedshops\Functional;

use Drupal\Tests\commerce_order\Functional\OrderBrowserTestBase;
use Drupal\commerce_trustedshops\Entity\Shop;
use Drupal\Tests\commerce_trustedshops\Traits\DeprecationSuppressionTrait;

/**
 * Tests the action to invite a custom to review an order.
 *
 * @group commerce_trustedshops
 * @group commerce_trustedshops_functional
 */
class InviteReviewTest extends OrderBrowserTestBase {
  use DeprecationSuppressionTrait;

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
  protected static $modules = [
    'commerce_trustedshops',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'starterkit_theme';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

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
   * Tests admin or people with permission can view invite-to-review form.
   */
  public function testInviteReviewFormAccess() {
    // Ensure even an admin can see the collection page.
    $this->drupalGet('/admin/commerce/orders/' . $this->order->id() . '/trustedshops/invite_review_confirm');
    $this->assertSession()->statusCodeEquals(200);

    // Login with an user without the proper permission cannot see the shop
    // admin view and receive a 403 error code.
    $admin_user = $this->drupalCreateUser([]);
    $this->drupalLogin($admin_user);
    $this->drupalGet('/admin/commerce/orders/' . $this->order->id() . '/trustedshops/invite_review_confirm');
    $this->assertSession()->statusCodeEquals(403);

    // Logout and check that anonymous users cannot see the shop admin view
    // and receive a 403 error code.
    $this->drupalLogout();
    $this->drupalGet('/admin/commerce/orders/' . $this->order->id() . '/trustedshops/invite_review_confirm');
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Tests warning messages when accessing the invite-to-review form.
   *
   * Test on an un-configured/mis-configured TrustedShops API Credentials.
   */
  public function testInviteReviewFormUnconfiguredCredential() {
    // When TrustedShops API credentials are empty, a warning should be shown.
    $config = \Drupal::configFactory()->getEditable('commerce_trustedshops.settings');
    $config->set('api.username', '');
    $config->set('api.password', '');
    $config->save();

    // Ensure even an admin can see the collection page.
    $this->drupalGet('/admin/commerce/orders/' . $this->order->id() . '/trustedshops/invite_review_confirm');
    $this->assertSession()->statusCodeEquals(403);
    $this->assertSession()->pageTextContains('Please configure your TrustedShops credentials before inviting customer to review an order.');
  }

  /**
   * Tests warning messages when accessing the invite-to-review form.
   *
   * Tests on an Order which has no TrustedShop ID configured.
   */
  public function testInviteReviewFormUnconfiguredShop() {
    // Create another store which should not have a TrustedShop-IDs.
    // Used to ensure edge-case on our tests.
    $another_store = $this->createStore('Second store', 'second@example.com');

    $this->order->setStore($another_store);
    $this->order->save();

    $this->drupalGet('/admin/commerce/orders/' . $this->order->id() . '/trustedshops/invite_review_confirm');
    $this->assertSession()->statusCodeEquals(403);
    $this->assertSession()->pageTextContains('Please create a TrustedShop ID for the store Second store in Not specified before inviting customer to review an order.');
  }

  /**
   * Tests inviting a customer to review an order.
   */
  public function testInviteReview() {
    $this->drupalGet('/admin/commerce/orders/' . $this->order->id() . '/trustedshops/invite_review_confirm');

    // Check the integrity of the form.
    $this->assertSession()->optionExists('email_template', 'BEST_PRACTICE');
    $this->assertSession()->optionExists('email_template', 'CREATING_TRUST');
    $this->assertSession()->optionExists('email_template', 'CUSTOMER_SERVICE');

    $this->assertSession()->fieldDisabled('tsid');
    $this->assertSession()->fieldValueEquals('tsid', 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U');
    $this->assertSession()->fieldDisabled('language');
    $this->assertSession()->fieldValueEquals('language', 'English (en)');
  }

}
