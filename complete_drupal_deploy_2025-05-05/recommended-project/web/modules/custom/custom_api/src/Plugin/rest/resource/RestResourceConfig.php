<?php

declare(strict_types=1);

namespace Drupal\custom_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\user\Entity\User;
use Drupal\rest\ModifiedResourceResponse;

/**
 * Represents positions as resources.
 *
 * @RestResource(
 *   id = "rest_resource_config",
 *   label = @Translation("Real estate Custom REST API"),
 *   uri_paths = {
 *     "canonical" = "api/realestate/{id}",
 *      "create" = "api/realestate"
 *   }
 * )
 */
final class RestResourceConfig extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   *   The response containing real estate data.
   */

  public function get(): ModifiedResourceResponse {
    //Hämtar id för inloggad användare
    $current_user_id = \Drupal::currentUser()->id();

    // databas fråga för att hämta fastighet
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'realestate')
      ->condition('uid', $current_user_id)
      ->accessCheck(TRUE)
      ->execute();

    //kör fråga
    $nodes = \Drupal\node\Entity\Node::loadMultiple($query);

    //array som ska returneras som svar
    $data = [];

    // Loopar igenom alla hämtade object
    foreach ($nodes as $node) {
      $data[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        'autoinvoice' => $node->get('field_autoinvoice')->value,
        'invoice_due_date' => $node->get('field_invoice_due_date')->value,
        'invoice_send_date' => $node->get('field_invoice_send_date')->value,
        'streetaddress' => $node->get('field_streetaddress')->value,
        'payment_method' => $node->get('field_payment_method')->value,
        'payment_number' => $node->get('field_payment_number')->value,
      ];
    }

    return new ModifiedResourceResponse($data, 200);
  }

  /**
   * Responds to POST requests.
   *
   *   lägger till en till node med fastighet.
   */
  public function post(array $data): ModifiedResourceResponse {

    //kontrollerar om data finns
    if (empty($data['title'])) {
      return new ModifiedResourceResponse(['error' => 'Title is required.'], 406);
    }
    if (empty($data['streetaddress'])) {
      return new ModifiedResourceResponse(['error' => 'Street Address is required.'], 406);
    }

    // deklarera node data och skapar noden
    $node = \Drupal\node\Entity\Node::create([
      'type' => 'realestate',
      'title' => $data['title'],
      'field_streetaddress' => $data['streetaddress'],
      'field_autoinvoice' => $data['autoinvoice'] ?? FALSE,
      'field_invoice_due_date' => $data['invoice_due_date'] ?? NULL,
      'field_invoice_send_date' => $data['invoice_send_date'] ?? NULL,
      'field_payment_method' => $data['payment_method'] ?? NULL,
      'field_payment_number' => $data['payment_number'] ?? NULL,
    ]);

    // Spara noden
    $node->save();

    // Returnera en framgångsrespons
    return new ModifiedResourceResponse($node, 201);
  }

  /**
   * Responds to DELETE requests.
   *
   *
   * Tar bort en nod med fastighet.
   */
  public function delete($id): ModifiedResourceResponse {

    // Hämta noden med det angivna ID:t
    $node = $this->getNodeById($id);

    //hämtar id för inloggad användare
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
      // Om noden inte tillhör användaren
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
   * Tar bort en nod med fastighet.
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
        // Uppdatera nodens fält om data finns
        if (isset($data['title'])) {
          $node->setTitle($data['title']);
        }
        if (isset($data['streetaddress'])) {
          $node->set('field_streetaddress', $data['streetaddress']);
        }
        if (isset($data['autoinvoice'])) {
          $node->set('field_autoinvoice', $data['autoinvoice']);
        }
        if (isset($data['invoice_due_date'])) {
          $node->set('field_invoice_due_date', $data['invoice_due_date']);
        }
        if (isset($data['invoice_send_date'])) {
          $node->set('field_invoice_send_date', $data['invoice_send_date']);
        }
        if (isset($data['payment_method'])) {
          $node->set('field_payment_method', $data['payment_method']);
        }
        if (isset($data['payment_number'])) {
          $node->set('field_payment_number', $data['payment_number']);
        }

        // Spara noden
        $node->save();

        // Returnerar ändrat object
        return new ModifiedResourceResponse($node, 201);
      }
      // Om noden inte tillhör användaren
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
