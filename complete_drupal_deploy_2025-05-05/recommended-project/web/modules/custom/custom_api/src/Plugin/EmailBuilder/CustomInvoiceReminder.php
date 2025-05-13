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
 *   id = "custom_invoice_reminder",
 *   sub_types = { "resend_invoice" = @Translation("resend_invoice") },
 * )
 */
class CustomInvoiceReminder extends EmailBuilderBase {

  /**
   * Saves the parameters for a newly created email.
   *
   * @param \Drupal\symfony_mailer\EmailInterface $email
   *   The email to modify.
   * @param mixed $to
   *   The to addresses, see Address::convert().
   */
  public function createParams(EmailInterface $email, $node = NULL, ) {
    // Tilldelar $email medföljande parametrar
    $email->setParam('node', $node);
  }

  /**
   * {@inheritdoc}
   */
  public function build(EmailInterface $email) {
    //Deklarerar variabler för medföljande parametrar
    $invoiceNode = $email->getParam('node');

    //Skapar ett datumobjekt för dagens datum
    $today = date('Y-m-d');


    //Samma html skickas även till en funktion för att skapa en pdf-fil
    $pdf = $this->generatePdf($invoiceNode->get('field_invoice_html')->value);

    //Deklarera emailadress, subjekt, bifogar pdf-filen samt skickar med html som body
    $email->setTo($invoiceNode->get('field_email')->value);
    $email->setSubject('Hyresavi: Påminnelse ' . $today);
    $email->attachNoPath($pdf, 'rent_invoice.pdf');
    $email->setBody(['#markup' => $invoiceNode->get('field_invoice_html')->value]);

    $this->updateInvoiceNodeStatus($invoiceNode);
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

  //Funktion som uppdaterar och lägger till fakturans html i sin helhet till raden för invoice_html
  public function updateInvoiceNodeStatus($node) {
    // Uppdaterar invoice-noden med html
    $node->set('field_invoice_status', 'reminded');
    $node->save();
  }

}
