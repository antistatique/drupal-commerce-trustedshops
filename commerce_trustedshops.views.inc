<?php

/**
 * @file
 * Provides views data for the commerce_trustedshops module.
 */

/**
 * Implements hook_views_data_alter().
 */
function commerce_trustedshops_views_data_alter(array &$data) {
  $data['commerce_order']['order_link_invite_review'] = [
    'field' => [
      'title' => t('TrustedShops: Link to invite writing a review of Order'),
      'id' => 'order_link_invite_review',
    ],
  ];
}
