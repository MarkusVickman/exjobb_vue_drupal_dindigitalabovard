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
 *   id = "rest_invoice",
 *   label = @Translation("Invoice Custom REST API"),
 *   uri_paths = {
 *     "canonical" = "api/invoice/{id}",
 *      "create" = "api/invoice/{id}"
 *   }
 * )
 */
final class RestInvoice extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   *   The response containing invoice data.
   */

  public function get(): ModifiedResourceResponse {

    // \Drupal::logger('rest_invoice')->notice(\Drupal::currentUser()->getAccountName());

    //hämtar id för inloggad användare
    $current_user_id = \Drupal::currentUser()->id();

    // databas fråga för att hämta faktura
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'invoice')
      ->condition('uid', $current_user_id)
      ->accessCheck(TRUE)
      ->execute();

    //kör fråga
    $nodes = \Drupal\node\Entity\Node::loadMultiple($query);

    //array för objekt som ska returneras
    $data = [];

    // Loopar igenom alla hämtade object
    foreach ($nodes as $node) {
      $data[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        'tenant_name' => $node->get('field_tenant_name')->value,
        'invoice_number' => $node->get('field_invoice_number')->value,
        'accommodationId' => $node->get('field_accommodation')->getValue(),
        'email' => $node->get('field_email')->value,
        'status' => $node->get('field_invoice_status')->value,
        'invoice_html' => $node->get('field_invoice_html')->value,
        'created' => $node->getCreatedTime(),
      ];
    }

    return new ModifiedResourceResponse($data, 200);
  }

  /**
   * Responds to POST requests.
   *
   *   lägger till en till node med invoice och skickar mail.
   */
  public function post($id): ModifiedResourceResponse {

    // Hämtar inloggad användares id
    $current_user_id = \Drupal::currentUser()->id();

    // Hämta noden med det angivna ID:t
    $accommodation_node = $this->getNodeById($id);

    if($accommodation_node->getOwnerId() == $current_user_id) {
      $reID = $accommodation_node->get('field_realestateid')->target_id;

      $node = $this->getNodeById($reID);

      // Hämta användarens ID från noden
      $user_id = $accommodation_node->get('uid')->target_id;

      // Laddar användarobjektet med användarens ID
      $user = User::load($user_id);

      // Hämtar användarnamnet
      $username = $user->getAccountName();

      $email_factory = Drupal::service('email_factory');
      $email = $email_factory->sendTypedEmail('custom_invoice', 'invoice', $node, $accommodation_node, $username);

      // Returnera en respons
      return new ModifiedResourceResponse('Invoice created and sent', 201);
    }else {
      // Returnera en respons
      return new ModifiedResourceResponse('Unauthorized user', 401);
    }
  }

  /**
   * Responds to DELETE requests.
   *
   *
   * Tar bort en nod med faktura.
   */
  public function delete($id): ModifiedResourceResponse {

    // Hämta noden med det angivna ID:t
    $node = $this->getNodeById($id);

    // Hämtar inloggad användares id
    $current_user_id = \Drupal::currentUser()->id();

    // Kontrollera om noden finns
    if ($node) {
      // Kontrollera om noden tillhör den inloggade användaren
      if ($node->getOwnerId() === $current_user_id) {
        // Ta bort noden
        $node->delete();

        // Return att borttaget
        return new ModifiedResourceResponse(NULL, 204);

      }
      // om inte Noden tillhör användaren
      else {
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('You do not have access to this node.');
      }
    }
    // Returnera ett fel om noden inte hittades
    else {
      return new ModifiedResourceResponse(['error' => 'Node not found'], 404);
    }
  }

  /**
   * Responds to PATCH requests.
   *
   *
   *
   * Tar bort en nod med fakturor.
   */
  public function patch($id, array $data): ModifiedResourceResponse {
    // Hämta noden med det angivna ID:t
    $node = $this->getNodeById($id);

    //Hämtar id för inloggad användare
    $current_user_id = \Drupal::currentUser()->id();

    // Kontrollera om noden finns
    if ($node) {
      // Kontrollera om noden tillhör den inloggade användaren
      if ($node->getOwnerId() === $current_user_id) {
        // Uppdatera nodens fält
        if (isset($data['status'])) {
          $node->set('field_invoice_status', $data['status']);
        }

        // Spara noden
        $node->save();

        // Returnerar ändrat object
        return new ModifiedResourceResponse($node, 201);
      }
      // om noden inte tillhör användaren
      else {
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('You do not have access to this node.');
      }
    }
    // Returnera ett fel om noden inte hittades
    else {
      return new ModifiedResourceResponse(['error' => 'Node not found'], 404);
    }
  }

  // En metod för att hämta noden baserat på ID
  private function getNodeById($nodeId) {
    // Här hämtas en nod från id parameter
    return \Drupal\node\Entity\Node::load($nodeId);
  }

}
