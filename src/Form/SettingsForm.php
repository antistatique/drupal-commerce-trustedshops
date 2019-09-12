<?php

namespace Drupal\commerce_trustedshops\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Xss;

/**
 * Configure Commerce TrustedShops settings form.
 *
 * @internal
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_trustedshops_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['commerce_trustedshops.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('commerce_trustedshops.settings');

    // Get configurations values for both optional API credentials.
    $username = $config->get('api.username');
    $password = $config->get('api.password');

    $form['api'] = [
      '#type' => 'details',
      '#title' => $this->t('TrustedShops API credentials'),
      // Close the details by default when any API credentials are fill.
      '#open' => $username || $password ? FALSE : TRUE,
    ];
    $form['api']['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#description' => $this->t('Your TrustedShops username for restricted API access.'),
      '#default_value' => $config->get('api.username'),
    ];
    $form['api']['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#description' => $this->t('Your TrustedShops password for restricted API access.'),
      '#default_value' => $config->get('api.password'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $username = Xss::filter($values['username'], []);
    $password = Xss::filter($values['password'], []);

    $config = $this->config('commerce_trustedshops.settings');
    $config->set('api.username', $username)->save();
    $config->set('api.password', $password)->save();

    parent::submitForm($form, $form_state);
  }

}
