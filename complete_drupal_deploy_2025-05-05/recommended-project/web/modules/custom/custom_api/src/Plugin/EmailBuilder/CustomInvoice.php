<?php

namespace Drupal\custom_api\Plugin\EmailBuilder;

use Drupal\Core\Render\Markup;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Processor\EmailBuilderBase;
use Mpdf\Mpdf;

/**
 * Defines the Email Builder plug-in for test mails.
 *
 * @EmailBuilder(
 *   id = "custom_invoice",
 *   sub_types = { "invoice" = @Translation("Invoice") },
 * )
 */
class CustomInvoice extends EmailBuilderBase {

  /**
   * Saves the parameters for a newly created email.
   *
   * @param \Drupal\symfony_mailer\EmailInterface $email
   *   The email to modify.
   * @param mixed $to
   *   The to addresses, see Address::convert().
   */
  public function createParams(EmailInterface $email, $realestate = NULL, $accommodation = NULL, $username = NULL) {
    // Tilldelar $email medföljande parametrar
    $email->setParam('realestate', $realestate);
    $email->setParam('accommodation', $accommodation);
    $email->setParam('username', $username);
  }

  /**
   * {@inheritdoc}
   */
  public function build(EmailInterface $email) {
    //Deklarerar variabler fär medföljande parametrar
    $realestate = $email->getParam('realestate');
    $accommodation = $email->getParam('accommodation');
    $username = $email->getParam('username');

    //Skapar ett datumobjekt för dagens datum
    $today = date('Y-m-d');
    //Skapar ett datumobjekt med nuvarande år samt dag. Det läggs sedan till värdet från fastighetsnoden för betalningsdag.
    $dueDate = date('Y-m') . "-" . sprintf('%02d', (int) $realestate->get('field_invoice_due_date')->value);

    //Om förfallodag redan har passerat för denna månad ändras förfallodag till månaden efter.
    if ($dueDate < $today) {
      // Om förfallodatumet har passerat, läggs en månad till
      $dueDate = date('Y-m-d', strtotime($dueDate . ' +1 month'));
    }

    //En ny node skapas för denna invoice
    $invoice_node = $this->createInvoiceNode($accommodation);

    //HTML komponeras utifrån fastighetsnode, bostadsnode, användarnamn samt datumen som skapades ovan.
    $html =
      '<h1 style="font-size:24px;text-decoration: underline;">Hyresavi från <b>' . $username . '</b> </h1><br>' .
      '<h2 style="font-size:20px;text-decoration: underline;">Boendeinformation</h2>' .
      '<p><b>Namn:</b> ' . $accommodation->get('field_tenant')->value . '</p>' .
      '<p><b>Adress:</b> ' . $realestate->get('field_streetaddress')->value . '</p>' .
      '<p><b>E-postadress:</b> ' . $accommodation->get('field_emailadress')->value . '</p><br>' .
      '<h2  style="font-size:20px;text-decoration: underline;">Faktura uppgifter</h2>' .
      '<p><b>Bostadsbeteckning:</b> ' . $accommodation->getTitle() . '</p>' .
      '<p><b>Faktureringsdatum:</b> ' . $today . '</p>' .
      '<p><b>Förfallodatum:</b> ' . $dueDate . '</p>' .
      '<p><b>Betalningsmetod:</b> ' . $realestate->get('field_payment_method')->value . '</p>' .
      '<p><b>Kontonummer:</b> ' . $realestate->get('field_payment_number')->value . '</p>' .
      '<p><b>Betalningsnummer:</b> ' . $invoice_node->get('field_invoice_number')->value . '</p>' .
      '<p><b>Hyra:</b> ' . $accommodation->get('field_rent')->value . 'kr</p><br>' .
      '<p>Betala <b>' . $accommodation->get('field_rent')->value . '</b>kr med <b>' . $realestate->get('field_payment_method')->value . '</b> till konto <b>' . $realestate->get('field_payment_number')->value . '</b>. Bifoga betalningsnummer: <b>' . $invoice_node->get('field_invoice_number')->value . '</b></p><br>';

    //Den nyskapade invoice noden samt html skickas till en funktion för att uppdateras med html
    $this->updateInvoiceNodeHTML($invoice_node, $html);

    //Samma html skickas även till en funktion för att skapa en pdf-fil
    $pdf = $this->generatePdf($html);

    //Deklarera emailadress, subjekt, bifogar pdf-filen samt skickar med html som body
    $email->setTo($accommodation->get('field_emailadress')->value);
    $email->setSubject('Hyresavi: ' . $realestate->getTitle());
    $email->attachNoPath($pdf, 'rent_invoice.pdf');
    $email->setBody(['#markup' => $html]);
  }

  //Funktion för att generera pdf från html.
  public function generatePdf($html): string {
    // Skapa en ny instans av mPDF.
    $mpdf = new Mpdf();

    // Skriv HTML-innehållet till PDF.
    $mpdf->WriteHTML($html);

    // Få PDF-innehållet som en sträng
    $pdf = $mpdf->Output('rent_invoice.pdf', 'S');
    return $pdf;
  }

  //funktion som skapar en ny node för invoice baserat på bostadsnoden.
  public function createInvoiceNode($accommodation) {
    // deklarera node data
    $node = \Drupal\node\Entity\Node::create([
      'type' => 'invoice',
      'title' => $accommodation->getTitle(),
      'field_accommodation' => $accommodation->id(),
      'field_email' => $accommodation->get('field_emailadress')->value,
      'field_tenant_name' => $accommodation->get('field_tenant')->value ?? '',
      'field_invoice_status' => 'not_payed'
    ]);

    // Sätt ägaren av noden till aktuella "ägaren"
    $uid = $accommodation->getOwnerId();
    $node->setOwnerId($uid);

    // Spara noden
    $node->save();

    return $node;
  }

  //Funktion som uppdaterar och lägger till fakturans html i sin helhet till raden för invoice_html
  public function updateInvoiceNodeHTML($node, $html) {
    // Uppdaterar invoice-noden med html
    $node->set('field_invoice_html', $html);
    $node->save();
  }

}
