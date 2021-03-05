<?php

namespace Drupal\Tests\commerce_trustedshops\Functional;

use Drupal\Tests\commerce\Functional\CommerceBrowserTestBase;
use Drupal\commerce_trustedshops\Entity\Shop;

/**
 * Tests the commerce_trustedshops_shop entity forms.
 *
 * @group commerce_trustedshops
 * @group commerce_trustedshops_functional
 */
class ShopAdminTest extends CommerceBrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'commerce_trustedshops',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected function getAdministratorPermissions() {
    return array_merge([
      'administer commerce trustedshops',
    ], parent::getAdministratorPermissions());
  }

  /**
   * Tests admin or people with permission can view the shop admin.
   */
  public function testAdminShopView() {
    $shop = Shop::create([
      'tsid' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store' => $this->store,
    ]);
    $shop->save();

    // Ensure even an admin can see the collection page.
    $this->drupalGet('/admin/commerce/trustedshops/shops');
    $shop_tsid = $this->getSession()->getPage()->findAll('css', 'tr td.views-field-tsid');
    $this->assertEquals(1, count($shop_tsid), 'Shop exists in the table.');

    // Login with an user without the proper permission cannot see the shop
    // admin view and receive a 403 error code.
    $admin_user = $this->drupalCreateUser([]);
    $this->drupalLogin($admin_user);
    $this->drupalGet('/admin/commerce/trustedshops/shops');
    $this->assertSession()->statusCodeEquals(403);

    // Logout and check that anonymous users cannot see the shop admin view
    // and receive a 403 error code.
    $this->drupalLogout();
    $this->drupalGet('/admin/commerce/trustedshops/shops');
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Tests creating a Shop.
   */
  public function testCreateShop() {
    // Create a shop through the add form.
    $this->drupalGet('/admin/commerce/trustedshops/shops');
    $this->assertSession()->statusCodeEquals(200);
    $this->getSession()->getPage()->clickLink('Add TrustedShops-ID');

    // Check the integrity of the form, the store field should be hidden as we
    // only have 1 store available.
    $this->assertSession()->fieldExists('tsid[0][value]');
    $this->assertSession()->fieldNotExists('store[target_id][value]');

    // Submit form.
    $edit = [
      'tsid[0][value]' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
    ];
    $this->submitForm($edit, 'Save');
    $this->assertSession()->statusCodeEquals(200);

    // Ensure the shop has been created in the default store.
    $this->container->get('entity_type.manager')->getStorage('commerce_trustedshops_shop')->resetCache([1]);
    $shop = Shop::load(1);
    $this->assertEquals('RCGABMX17MMTAF9V97G9DZEAKG1EILO0U', $shop->getTsid());
    $this->assertEquals($this->store, $shop->getStore());
  }

  /**
   * Tests creating a Shop with two available stores.
   */
  public function testCreateShopMultipleStore() {
    $another_store = $this->createStore('Second store', 'second@example.com');
    $this->drupalGet('admin/commerce/trustedshops/shops/add');

    // Check the integrity of the form, the store field should be visible as
    // we have 2 available stores.
    $this->assertSession()->fieldExists('tsid[0][value]');
    $this->assertSession()->fieldExists('store[target_id][value]');

    // Submit form.
    $edit = [
      'tsid[0][value]' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store[target_id][value]' => $another_store->id(),
    ];
    $this->submitForm($edit, 'Save');
    $this->assertSession()->statusCodeEquals(200);

    // Ensure the shop has been created in the selected store.
    $this->container->get('entity_type.manager')->getStorage('commerce_trustedshops_shop')->resetCache([1]);
    $shop = Shop::load(1);
    $this->assertEquals('RCGABMX17MMTAF9V97G9DZEAKG1EILO0U', $shop->getTsid());
    $this->assertEquals($another_store, $shop->getStore());
  }

  /**
   * Tests editing a shop.
   */
  public function testEditShop() {
    $shop = Shop::create([
      'tsid' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store[target_id][value]' => $this->store->id(),
    ]);
    $shop->save();

    $this->drupalGet($shop->toUrl('edit-form'));

    // Check the integrity of the form, the store field should be hidden as we
    // only have 1 store available.
    $this->assertSession()->fieldValueEquals('tsid[0][value]', 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U');
    $this->assertSession()->fieldNotExists('store[target_id][value]');

    // Submit form.
    $edit = [
      'tsid[0][value]' => 'ADVXFLMZHXXLEBXSPXMPEAXOWYBXKMGNB',
    ];
    $this->submitForm($edit, 'Save');
    $this->assertSession()->statusCodeEquals(200);

    // Ensure the shop has been saved.
    $this->container->get('entity_type.manager')->getStorage('commerce_trustedshops_shop')->resetCache([1]);
    $shop = Shop::load(1);
    $this->assertEquals('ADVXFLMZHXXLEBXSPXMPEAXOWYBXKMGNB', $shop->getTsid());
    $this->assertEquals($this->store, $shop->getStore());
  }

  /**
   * Tests editing a Shop with two available stores.
   */
  public function testEditShopMultipleStore() {
    $another_store = $this->createStore('Second store', 'second@example.com');

    $shop = Shop::create([
      'tsid' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store' => $this->store,
    ]);
    $shop->save();

    $this->drupalGet($shop->toUrl('edit-form'));

    // Check the integrity of the form, the store field should be visible as
    // we have 2 available stores.
    $this->assertSession()->fieldValueEquals('tsid[0][value]', 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U');
    $this->assertSession()->checkboxChecked('edit-store-target-id-value-1');
    $this->assertSession()->checkboxNotChecked('edit-store-target-id-value-2');

    // Submit form.
    $edit = [
      'tsid[0][value]' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store[target_id][value]' => $another_store->id(),
    ];
    $this->submitForm($edit, 'Save');
    $this->assertSession()->statusCodeEquals(200);

    // Ensure the shop has been saved.
    $this->container->get('entity_type.manager')->getStorage('commerce_trustedshops_shop')->resetCache([1]);
    $shop = Shop::load(1);
    $this->assertEquals('RCGABMX17MMTAF9V97G9DZEAKG1EILO0U', $shop->getTsid());
    $this->assertEquals($another_store, $shop->getStore());
  }

  /**
   * Tests editing a shop after the store was deleted.
   */
  public function testEditShopWithDeletedStore() {
    $shop = Shop::create([
      'tsid' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store' => $this->store,
    ]);
    $shop->save();
    $this->store->delete();

    $this->drupalGet($shop->toUrl('edit-form'));
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm([], 'Save');
    $this->assertSession()->statusCodeEquals(200);

    // Ensure the shop has been saved.
    $this->container->get('entity_type.manager')->getStorage('commerce_trustedshops_shop')->resetCache([$shop->id()]);
    $shop = Shop::load($shop->id());
    $this->assertEquals('RCGABMX17MMTAF9V97G9DZEAKG1EILO0U', $shop->getTsid());
    $this->assertNull($shop->getStore());
  }

  /**
   * Tests deleting a shop.
   */
  public function testDeleteShop() {
    $shop = Shop::create([
      'tsid' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store' => $this->store,
    ]);
    $shop->save();

    $this->drupalGet($shop->toUrl('delete-form'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains(sprintf('Are you sure you want to delete the trustedshops-ids %s?', $shop->label()));
    $this->assertSession()->pageTextContains('This action cannot be undone.');
    $this->submitForm([], 'Delete');

    $this->container->get('entity_type.manager')->getStorage('commerce_trustedshops_shop')->resetCache([$shop->id()]);
    $shop_exists = (bool) Shop::load($shop->id());
    $this->assertEmpty($shop_exists, 'The shop has been deleted from the database.');
  }

  /**
   * Tests that admin can view a shop's details.
   */
  public function testAdminShopCanonical() {
    $shop = Shop::create([
      'tsid' => 'RCGABMX17MMTAF9V97G9DZEAKG1EILO0U',
      'store' => $this->store,
    ]);
    $shop->save();

    // Ensure even an admin can see the canonical detail page.
    $this->drupalGet($shop->toUrl()->toString());
    $this->assertSession()->statusCodeEquals(200);

    // Logout and check that anonymous users cannot see the shop admin screen
    // and receive a 403 error code.
    $this->drupalLogout();

    $this->drupalGet($shop->toUrl()->toString());
    $this->assertSession()->statusCodeEquals(403);
  }

}
