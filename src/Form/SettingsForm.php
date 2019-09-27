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

    $form['mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('E nvironment'),
      '#options' => [
        1 => $this->t('Test (QA)'),
        0 => $this->t('Production'),
      ],
      '#description' => $this->t('Use the Test/QA environment if you want to tests the module. Otherwhise, you risk to send review & emails to your end-user and potentialy badly collect your e-shop reviews..'),
      '#default_value' => $config->get('test_mode') ?: 0,
    ];

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

    $mode = Xss::filter($values['mode'], []);
    $username = Xss::filter($values['username'], []);
    $password = Xss::filter($values['password'], []);

    $config = $this->config('commerce_trustedshops.settings');
    $config->set('test_mode', (bool) $mode)->save();
    $config->set('api.username', $username)->save();
    $config->set('api.password', $password)->save();

    parent::submitForm($form, $form_state);
  }

}
