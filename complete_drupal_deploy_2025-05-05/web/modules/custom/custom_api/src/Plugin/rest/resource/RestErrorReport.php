<?php

declare(strict_types=1);

namespace Drupal\custom_api\Plugin\rest\resource;

use Drupal;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\user\Entity\User;

/**
 * Represents positions as resources.
 *
 * @RestResource(
 *   id = "rest_error_report",
 *   label = @Translation("Error Report Custom REST API"),
 *   uri_paths = {
 *     "canonical" = "api/error_report/{id}",
 *      "create" = "api/error_report"
 *   }
 * )
 */
final class RestErrorReport extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   *   The response containing error report data.
   */

  public function get(): ModifiedResourceResponse {
    //OBS!! denna går nu att göra om så att alla noder hämtas som tillhör användaren istället för att först hämta accommodation

    //hämtar aktiva användaren uid
    $current_user_id = \Drupal::currentUser()->id();

    // query för att hämta accommodation som tillgör användaren
    $queryAccommodation = \Drupal::entityQuery('node')
      ->condition('type', 'accommodation')
      ->condition('uid', $current_user_id)
      ->accessCheck(TRUE)
      ->execute();

    //kör query
    $accNodes = \Drupal\node\Entity\Node::loadMultiple($queryAccommodation);

    //array för att returnera objekt
    $data = [];

    //För varje bostadsnode
    foreach ($accNodes as $accNode) {
      // query för error_report. Hämtar alla noder som tillhör bostaden
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'error_report')
        ->condition('field_accommodation_ref', $accNode->id())
        ->accessCheck(FALSE)
        ->execute();

      //Kör frågan
      $nodes = \Drupal\node\Entity\Node::loadMultiple($query);

      //För varje felrapport lagras ett objekt till data[]
      foreach ($nodes as $node) {
        $data[] = [
          'id' => $node->id(),
          'title' => $node->getTitle(),
          'email' => $node->get('field_emailaddress')->value,
          'name' => $node->get('field_name')->value,
          'message' => $node->get('field_message')->value,
          'messagetype' => $node->get('field_messagetype')->value,
          //'accommodation_ref' => $accNode->Id(),
          //testar skicka med namn istället då id troligen inte är nödvändigt i detta fall
          'accommodation_ref' => $accNode->getTitle(),
          'status' => $node->get('field_status')->value,
          'created' => $node->getCreatedTime(),
        ];
      }
    }

    return new ModifiedResourceResponse($data, 200);
    //return new ResourceResponse($current_user_id, 201);
  }

  /**
   * Responds to POST requests.
   *
   *   The response containing error reported data to residents.
   */

  public function post(array $data): ModifiedResourceResponse {
    //Kontrollerar att alla required värden är ifyllda. Annars returneras 406
    if (empty($data['title'])) {
      return new ModifiedResourceResponse(['error' => 'Title is required.'], 406);
    }

    if (empty($data['emailaddress'])) {
      return new ModifiedResourceResponse(['error' => 'Email is required.'], 406);
    }

    if (empty($data['message'])) {
      return new ModifiedResourceResponse(['error' => 'Message is required.'], 406);
    }

    if (empty($data['messagetype'])) {
      return new ModifiedResourceResponse(['error' => 'Message type is required.'], 406);
    }

    if (empty($data['accommodation_ref'])) {
      return new ModifiedResourceResponse(['error' => 'Real Estate Id is required.'], 406);
    }

    // Load the node by nid.
    $accNode = \Drupal\node\Entity\Node::load($data['accommodation_ref']);

    // deklarera node data och skapar en ny error report node
    $node = \Drupal\node\Entity\Node::create([
      'type' => 'error_report',
      'title' => $data['title'],
      'field_emailaddress' => $data['emailaddress'],
      'field_name' => $accNode->get('field_tenant')->value,
      'field_accommodation_ref' => $data['accommodation_ref'],
      'field_message' => $data['message'],
      'field_messagetype' => $data['messagetype'],
      'field_status' => 'not_started',
    ]);

    // Sätt ägaren av den nya error report till ägaren av bostaden
    $uid = $accNode->getOwnerId();
    $node->setOwnerId($uid);
    // Laddar användarobjektet med användarens ID
    $user = User::load($uid);

    // Spara noden
    $result = $node->save();

    //Skickar e-post till fastighetsägaren
    if ($result) {
      $email_factory = Drupal::service('email_factory');
      $email_factory->sendTypedEmail('custom_fault_report', 'send_fault_notice', $accNode->getTitle(), $user->getEmail());
    }

    // Returnera en framgångsrespons
    return new ModifiedResourceResponse($node, 201);
  }

  /**
   * Responds to DELETE requests.
   *
   *
   * Tar bort en nod med felmeddelande.
   */
  public function delete($id): ModifiedResourceResponse {
    // Hämta noden med det angivna ID:t (html-parameter)
    $node = $this->getNodeById($id);

    //Hämtar inloggad användares uid
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
      else {
        // Noden tillhör inte användaren, return exception
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('You do not have access to this node.');
      }
    }
    else {
      // Returnera ett fel om noden inte hittades
      return new ModifiedResourceResponse(['error' => 'Node not found'], 404);
    }
  }

  /**
   * Responds to PATCH requests.
   *
   *
   *
   * Uppdaterar en nod med felmeddelande.
   */
  public function patch($id, array $data): ModifiedResourceResponse {
    // Hämta noden med det angivna ID:t (html-parameter)
    $node = $this->getNodeById($id);

    //Hämtar inloggad användares uid
    $current_user_id = \Drupal::currentUser()->id();

    // Kontrollera om noden finns
    if ($node) {
      // Kontrollera om noden tillhör den inloggade användaren
      if ($node->getOwnerId() === $current_user_id) {
        // Uppdatera nodens fält
        if (isset($data['status'])) {
          $node->set('field_status', $data['status']);
        }

        // Spara noden
        $node->save();

        // Returnerar ändrat object
        return new ModifiedResourceResponse($node, 201);
      }
      else {
        // Noden tillhör inte användaren
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('You do not have access to this node.');
      }
    }
    else {
      // Returnera ett fel om noden inte hittades
      return new ModifiedResourceResponse(['error' => 'Node not found'], 404);
    }
  }

  // En metod för att hämta noden baserat på ID
  private function getNodeById($nodeId) {
    // Här hämtas en nod från id parameter
    return \Drupal\node\Entity\Node::load($nodeId);
  }

}
