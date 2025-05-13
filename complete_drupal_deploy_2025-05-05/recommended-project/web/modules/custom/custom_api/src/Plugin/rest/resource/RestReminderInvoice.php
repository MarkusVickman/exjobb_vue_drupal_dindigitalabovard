<?php

declare(strict_types=1);

namespace Drupal\custom_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\user\Entity\User;
use Drupal\rest\ModifiedResourceResponse;
use Drupal;

/**
 * Represents positions as resources.
 *
 * @RestResource(
 *   id = "rest_invoice_reminder",
 *   label = @Translation("Invoice reminder Custom REST API"),
 *   uri_paths = {
 *     "canonical" = "api/resend_invoice/{id}",
 *      "create" = "api/resend_invoice/{id}"
 *   }
 * )
 */
final class RestReminderInvoice extends ResourceBase {

  /**
   * Responds to POST requests.
   *
   *   Skickar ett påminnelse mail.
   */
  public function post($id): ModifiedResourceResponse {



    //Hämtar uid för aktiv användare
    $current_user_id = \Drupal::currentUser()->id();

    \Drupal::logger('rest_invoice_reminder')->notice(\Drupal::currentUser()->getAccountName());
    // Hämta noden med det angivna ID:t
    $invoiceNode = $this->getNodeById($id);

    // Hämta användarens ID från noden
    $user_id =  $invoiceNode->get('uid')->target_id;

    if($user_id == $current_user_id ) {

      $email_factory = Drupal::service('email_factory');
      $email_factory->sendTypedEmail('custom_invoice_reminder', 'resend_invoice', $invoiceNode);

      // Returnera en respons
      return new ModifiedResourceResponse('Invoice created and sent', 201);
    }else {
      // Returnera en respons
      return new ModifiedResourceResponse('Unauthorized user', 401);
    }

  }

  // En metod för att hämta noden baserat på ID
  private function getNodeById($nodeId) {
    // Här hämtas en nod från id parameter
    return \Drupal\node\Entity\Node::load($nodeId);
  }

}
