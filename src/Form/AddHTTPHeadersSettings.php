<?php

/**
 * @file
 * Contains \Drupal\http_response_headers\Form\AddHTTPHeadersSettings.
 */

namespace Drupal\http_response_headers\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure site information settings for this site.
 */
class AddHTTPHeadersSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'http_response_headers_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['http_response_headers.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('http_response_headers.settings');
    $security = $config->get('security');
    $performance = $config->get('performance');

    $form['config'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['security'] = [
      '#type'  => 'details',
      '#title' => t('Security'),
      '#group' => 'config',
    ];

    $form['security']['Content-Security-Policy'] = [
      '#type'          => 'textarea',
      '#title'         => $this->t('Content-Security-Policy'),
      '#default_value' => !empty($security['Content-Security-Policy']) ? $security['Content-Security-Policy'] : '',
      '#description'   => $this->t("This HTTP header parameter allows you to define a whitelist of approved sources of content for your site. By restricting the assets that a browser can load for your site you will have extra level of protection from XSS attacks."),
      '#attributes'    => [
        'placeholder' => "Example: default-src 'self';",
      ],
    ];

    $form['security']['Strict-Transport-Security'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Strict-Transport-Security'),
      '#default_value' => !empty($security['Strict-Transport-Security']) ? $security['Strict-Transport-Security'] : '',
      '#description'   => $this->t('This policy will enforce TLS on your site and all subdomains for a year.'),
      '#attributes'    => [
        'placeholder' => 'Example: max-age=31536000; includeSubDomains',
      ],
    ];

    $form['security']['Public-Key-Pins'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Public-Key-Pins'),
      '#default_value' => !empty($security['Public-Key-Pins']) ? $security['Public-Key-Pins'] : '',
      '#description'   => $this->t('HTTP Public Key Pinning (HPKP) is a security feature that tells a web client to associate a specific cryptographic public key with a certain web server to prevent Man in the Middle (MITM) attacks with forged certificates.'),
      '#attributes'    => [
        'placeholder' => '',
      ],
    ];

    $form['security']['X-Xss-Protection'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('X-Xss-Protection'),
      '#default_value' => !empty($security['X-Xss-Protection']) ? $security['X-Xss-Protection'] : '',
      '#description'   => $this->t("This response header can be used to configure a user-agent's built in reflective XSS protection. Currently, only Microsoft's Internet Explorer, Google Chrome and Safari (WebKit) support this header."),
      '#attributes'    => [
        'placeholder' => 'Example: 1; mode=block',
      ],
    ];

    $form['security']['X-Frame-Options'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('X-Frame-Options'),
      '#default_value' => !empty($security['X-Frame-Options']) ? $security['X-Frame-Options'] : '',
      '#description'   => $this->t("Clickjacking protection. Valid values include <em>DENY</em> meaning your site can't be framed, <em>SAMEORIGIN</em> which allows you to frame your own site or <em>ALLOW-FROM https://example.com/</em> which lets you specify sites that are permitted to frame your own site."),
      '#attributes'    => [
        'placeholder' => 'Example: SAMEORIGIN',
      ],
    ];

    $form['security']['X-Content-Type-Options'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('X-Content-Type-Options'),
      '#default_value' => !empty($security['X-Content-Type-Options']) ? $security['X-Content-Type-Options'] : '',
      '#description'   => $this->t('This header parameter prevents Google Chrome and Internet Explorer from trying to mime-sniff the content-type of a response away from the one being declared by the server.'),
      '#attributes'    => [
        'placeholder' => 'Example: nosniff',
      ],
    ];

    $form['performance'] = [
      '#type'  => 'details',
      '#title' => t('Performance'),
      '#group' => 'config',
    ];

    $form['performance']['Cache-Control'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Cache-Control'),
      '#default_value' => !empty($performance['Cache-Control']) ? $performance['Cache-Control'] : '',
      '#description'   => $this->t('<strong>Drupal already adds this. This is just an example and overriding this might hurt your website performance</strong>. The Cache-Control header is the most important header to set as it effectively ‘switches on’ caching in the browser. With this header in place, and set with a value that enables caching, the browser will cache the file for as long as specified. Without this header the browser will re-request the file on each subsequent request.'),
      '#attributes'    => [
        'placeholder' => 'Example: max-age=900, public',
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('http_response_headers.settings');

    // Security
    $security = [
      'Content-Security-Policy'   => $form_state->getValue('Content-Security-Policy'),
      'Strict-Transport-Security' => $form_state->getValue('Strict-Transport-Security'),
      'Public-Key-Pins'           => $form_state->getValue('Public-Key-Pins'),
      'X-Xss-Protection'          => $form_state->getValue('X-Xss-Protection'),
      'X-Frame-Options'           => $form_state->getValue('X-Frame-Options'),
      'X-Content-Type-Options'    => $form_state->getValue('X-Content-Type-Options'),
    ];

    // Performance
    $performance = [
      'Cache-Control' => $form_state->getValue('Cache-Control')
    ];

    // Save settings.
    $config
      // Security
      ->set('security', $security)
      // Performance
      ->set('performance', $performance)
      ->save();

    // Clear Drupal cache.
    drupal_flush_all_caches();

    parent::submitForm($form, $form_state);

  }

}
