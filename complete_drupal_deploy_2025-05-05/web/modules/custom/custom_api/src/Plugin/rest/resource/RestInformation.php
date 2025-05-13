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
 *   id = "rest_information",
 *   label = @Translation("Information Custom REST API"),
 *   uri_paths = {
 *     "canonical" = "api/information/{id}",
 *      "create" = "api/information"
 *   }
 * )
 */
final class RestInformation extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   *   The response containing information data to residents.
   */

  public function get(): ModifiedResourceResponse {
    //Läser in inloggad användares id
    $current_user_id = \Drupal::currentUser()->id();

    // databas-fråga för att hämta information till boende
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'information')
      ->condition('uid', $current_user_id)
      ->accessCheck(TRUE)
      ->execute();

    //kör frågan
    $nodes = \Drupal\node\Entity\Node::loadMultiple($query);

    //array för att lagra object till retur
    $data = [];

    // Loopar igenom alla hämtade object
    foreach ($nodes as $node) {

      // Hämtar relaterat realestate_id
      $realestate_ids = $node->get('field_realestate_id')->getValue();
      $realestate_title = '';

      // OBS!! Dessa if-satser används för att skicka med extra data i svaret. Det gör det tydligar i frontend när även namnet för fastigheten som informationen gäller skickas med.
      // Om det finns en referens till realestate
      if (!empty($realestate_ids)) {
        // Hämta den första realestate noden
        $realestate_node = \Drupal\node\Entity\Node::load($realestate_ids[0]['target_id']);
        if ($realestate_node) {
          $realestate_title = $realestate_node->getTitle();
        }
      }

      //Skickar med ett objekt till svaret för varje informationsnode
      $data[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        'information' => $node->get('field_information')->value,
        'realestate_id' => $node->get('field_realestate_id')->getValue(),
        'realestate_title' => $realestate_title, // Lägg till realestate_title
        'created' => $node->getCreatedTime(),
      ];
    }

    return new ModifiedResourceResponse($data, 200);
  }

  /**
   * Responds to POST requests.
   *
   *   lägger till en till node med fastigshetsinformation.
   */
  public function post(array $data): ModifiedResourceResponse {

    //Kontrollerar att required data är medskickat i post-anropet
    if (empty($data['title'])) {
      return new ModifiedResourceResponse(['error' => 'Title is required.'], 406);
    }

    if (empty($data['information'])) {
      return new ModifiedResourceResponse(['error' => 'Information text is required.'], 406);
    }

    if (empty($data['realestate_id'])) {
      return new ModifiedResourceResponse(['error' => 'real estate id is required.'], 406);
    }

    // deklarera node data och skapar node för information
    $node = \Drupal\node\Entity\Node::create([
      'type' => 'information',
      'title' => $data['title'],
      'field_information' => $data['information'],
      'field_realestate_id' => $data['realestate_id'],
    ]);

    // Spara noden
    $node->save();

    // Returnerar array med informations objekt
    return new ModifiedResourceResponse('Information node created', 201);
  }


  /**
   * Responds to DELETE requests.
   *
   *
   * Tar bort en nod med fastighetsinformation.
   */
  public function delete($id): ModifiedResourceResponse {
    // Hämta noden med det angivna ID:t
    $node = $this->getNodeById($id);

    // Hämtar id för inloggad användare
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
      // Om noden tillhör inte användaren
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
   * Tar bort en nod med fastighetsinformation.
   */
  public function patch($id, array $data): ModifiedResourceResponse {
    // Hämta noden med det angivna ID:t
    $node = $this->getNodeById($id);

    // Hämtar id för inloggad användare
    $current_user_id = \Drupal::currentUser()->id();

    // Kontrollera om noden finns
    if ($node) {
      // Kontrollera om noden tillhör den inloggade användaren
      if ($node->getOwnerId() === $current_user_id) {
        // Uppdatera nodens fält om datan finns
        if (isset($data['title'])) {
          $node->setTitle($data['title']);
        }
        if (isset($data['realestate_id'])) {
          $node->set('field_realestate_id', $data['realestate_id']);
        }
        if (isset($data['information'])) {
          $node->set('field_information', $data['information']);
        }

        // Spara noden
        $node->save();

        // Returnerar ändrat object
        return new ModifiedResourceResponse($node, 201);
      }
      // om noden tillhör inte användaren
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
