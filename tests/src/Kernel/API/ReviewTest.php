<?php

namespace Drupal\Tests\commerce_trustedshops\Kernel\API;

use Drupal\commerce_trustedshops\API\Review as TrustedShopsReview;
use Drupal\commerce_trustedshops\Event\AlterProductDataEvent;
use Drupal\commerce_trustedshops\Event\TrustedShopsEvents;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests the Shop entity.
 *
 * @coversDefaultClass \Drupal\commerce_trustedshops\API\Review
 *
 * @group commerce_trustedshops
 */
class ReviewTest extends APITestBase {

  /**
   * The event dispatcher prophecy.
   *
   * @var \Prophecy\Prophecy\ObjectProphecy
   */
  protected $eventDispatcher;

  /**
   * The Service to trigger invitations to review a shop.
   *
   * @var \Drupal\commerce_trustedshops\API\Review
   */
  protected $trustedShopsReview;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $config_factory = $this->container->get('config.factory');
    $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

    // Setup dummy TrustedShops Credentials API.
    $config = $config_factory->getEditable('commerce_trustedshops.settings');
    $config->set('test_mode', TRUE);
    $config->set('api.username', 'john.doe@example.org');
    $config->set('api.password', 'qwertz');
    $config->save();

    $this->trustedShopsReview = new TrustedShopsReview($this->trustedShops, $config_factory, $this->eventDispatcher->reveal());

    $this->trustedShops->expects($this->once())->method('setEndpoint')
      ->with('api-qa', 'restricted');

    $this->trustedShops->expects($this->once())->method('setApiCredentials')
      ->with('john.doe@example.org', 'qwertz');
  }

  /**
   * @covers ::triggerShopReview
   */
  public function testTriggerShopReview() {
    $now = \DateTime::createFromFormat('U', time());
    $now->setTimezone(new \DateTimeZone('UTC'));

    $this->eventDispatcher
      ->dispatch(Argument::type(AlterProductDataEvent::class), TrustedShopsEvents::ALTER_PRODUCT_DATA)
      ->shouldBeCalled();

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
                    'name' => 'Default testing product',
                    'url' => 'http://localhost/product/1',
                  ],
                ],
              ],
              'consumer' => [
                'firstname' => '',
                'lastname' => '',
                'contact' => [
                  'email' => 'john.doe@example.org',
                ],
              ],
            ],
          ],
        ],
      ]);

    $this->trustedShopsReview->triggerShopReview('BEST_PRACTICE', $this->order, $this->shop);
  }

  /**
   * @covers ::triggerShopReview
   */
  public function testTriggerShopReviewWithoutPurchasedEntity() {
    $now = \DateTime::createFromFormat('U', time());
    $now->setTimezone(new \DateTimeZone('UTC'));

    // Remove all Purchased Entity of order tests.
    foreach ($this->order->getItems() as $order_item) {
      $order_item->purchased_entity = NULL;
      $order_item->save();
    }

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
                    'name' => 'Default testing product',
                  ],
                ],
              ],
              'consumer' => [
                'firstname' => '',
                'lastname' => '',
                'contact' => [
                  'email' => 'john.doe@example.org',
                ],
              ],
            ],
          ],
        ],
      ]);

    $this->trustedShopsReview->triggerShopReview('BEST_PRACTICE', $this->order, $this->shop);
  }

}
