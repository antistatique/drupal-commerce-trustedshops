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
    'title' => t('TrustedShops invite review link'),
    'field' => [
      'title' => t('TrustedShops invite review link'),
      'id' => 'order_link_invite_review',
    ],
  ];
}
