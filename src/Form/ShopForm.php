<?php

namespace Drupal\commerce_trustedshops\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the TrustedShops-IDs add/edit forms.
 *
 * @internal
 */
class ShopForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    $form_state->setRedirect('entity.commerce_trustedshops_shop.collection');
  }

}
