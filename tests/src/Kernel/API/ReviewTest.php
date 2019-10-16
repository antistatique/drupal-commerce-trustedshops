<?php

namespace Drupal\Tests\commerce_trustedshops\Kernel\API;

use Drupal\commerce_trustedshops\API\Review as TrustedShopsReview;
use Drupal\Core\Language\Language;

/**
 * Tests the Shop entity.
 *
 * @coversDefaultClass \Drupal\commerce_trustedshops\API\Review
 *
 * @group commerce_trustedshops
 */
class ReviewTest extends APITestBase {

  /**
   * The Service to trigger invitations to review a shop.
   *
   * @var \Drupal\commerce_trustedshops\API\Review
   */
  protected $trustedShopsReview;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $config_factory = $this->container->get('config.factory');

    // Setup dummy TrustedShops Credentials API.
    $config = $config_factory->getEditable('commerce_trustedshops.settings');
    $config->set('test_mode', TRUE);
    $config->set('api.username', 'john.doe@example.org');
    $config->set('api.password', 'qwertz');
    $config->save();

    $this->trustedShopsReview = new TrustedShopsReview($this->trustedShops, $config_factory);
  }

  /**
   * @covers ::triggerShopReview
   */
  public function testTriggerShopReview() {
    $language = new Language(['id' => 'fr']);
    $now = \DateTime::createFromFormat('U', time());
    $now->setTimezone(new \DateTimeZone('UTC'));

    $this->trustedShops->expects($this->once())->method('setEndpoint')
      ->with('api-qa', 'restricted');

    $this->trustedShops->expects($this->once())->method('setApiCredentials')
      ->with('john.doe@example.org', 'qwertz');

    $this->trustedShops->expects($this->once())->method('post')
      ->with('shops/RCGABMX17MMTAF9V97G9DZEAKG1EILO0U/reviews/trigger.json', [
        'reviewCollectorRequest' => [
          'reviewCollectorReviewRequests' => [
            [
              'reminderDate' => $now->format('Y-m-d'),
              'template' => [
                'variant' => 'BEST_PRACTICE',
                'includeWidget' => 'true',
              ],
              'order' => [
                'orderDate' => '1990-02-25',
                'orderReference' => 'order-6',
                'currency' => 'USD',
                'estimatedDeliveryDate' => $now->format('Y-m-d'),
                'products' => [
                  0 => [
                    'sku' => 'TEST_p7gwvl76',
                    'name' => NULL,
                    'url' => 'http://localhost/product/1',
                  ],
                ],
              ],
              'consumer' => [
                'firstname' => '',
                'lastname' => '',
                'contact' => [
                  'email' => 'john.doe@example.org',
                  'language' => 'fr',
                ],
              ],
            ],
          ],
        ],
      ]);

    $this->trustedShopsReview->triggerShopReview('BEST_PRACTICE', $this->order, $this->shop, $language);
  }

}
