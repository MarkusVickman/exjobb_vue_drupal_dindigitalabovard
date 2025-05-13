<?php

namespace Drupal\custom_api\Plugin\EmailBuilder;

use Drupal\Core\Render\Markup;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Processor\EmailBuilderBase;
use Mpdf\Mpdf;

/**
 * Defines the Email Builder plug-in for fault notice mails.
 *
 * @EmailBuilder(
 *   id = "custom_fault_report",
 *   sub_types = { "send_fault_notice" = @Translation("send_fault_notice") },
 * )
 */
class CustomFaultReport extends EmailBuilderBase {

  /**
   * Saves the parameters for a newly created email.
   *
   * @param \Drupal\symfony_mailer\EmailInterface $email
   *   The email to modify.
   * @param mixed $to
   *   The to addresses, see Address::convert().
   */
  public function createParams(EmailInterface $email, $accommodation = NULL, $emailAddress = NULL ) {
    // Tilldelar $email medföljande parametrar
    $email->setParam('accommodation', $accommodation);
    $email->setParam('emailAddress', $emailAddress);
  }

  /**
   * {@inheritdoc}
   */
  public function build(EmailInterface $email) {
    //Deklarerar variabler för medföljande parametrar
    $accommodation = $email->getParam('accommodation');
    $emailAddress = $email->getParam('emailAddress');

    //Skapar ett datumobjekt för dagens datum
    $today = date('Y-m-d');

    $html =
      '<h1 style="font-size:24px;text-decoration: underline;">Felanmälan från lägenhet <b>' . $accommodation . '</b> </h1><br>' .
      '<p><b>Datum:</b> ' . $today . '</p>';

    //Deklarera emailadress, subjekt, bifogar pdf-filen samt skickar med html som body
    $email->setTo($emailAddress);
    $email->setSubject('Felanmälan från ' . $accommodation);
    $email->setBody(['#markup' => $html]);

  }
}
