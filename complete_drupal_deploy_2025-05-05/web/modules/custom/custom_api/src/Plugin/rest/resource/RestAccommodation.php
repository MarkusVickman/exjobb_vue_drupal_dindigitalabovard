<?php

declare(strict_types=1);

namespace Drupal\custom_api\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;

/**
 * Represents positions as resources.
 *
 * @RestResource(
 *   id = "rest_accommodation",
 *   label = @Translation("Accommodation Custom REST API"),
 *   uri_paths = {
 *     "canonical" = "/api/accommodation/{id}",
 *      "create" = "/api/accommodation"
 *   }
 * )
 * @DCG
 * The plugin exposes key-value records as REST resources. In order to enable it
 * import the resource configuration into active configuration storage. An
 * example of such configuration can be located in the following file:
 * core/modules/rest/config/optional/rest.resource.entity.node.yml.
 * Alternatively, you can enable it through admin interface provider by REST UI
 * module.
 * @see https://www.drupal.org/project/restui
 *
 * @DCG
 * Notice that this plugin does not provide any validation for the data.
 * Consider creating custom normalizer to validate and normalize the incoming
 * data. It can be enabled in the plugin definition as follows.
 * @code
 *   serialization_class = "Drupal\foo\MyDataStructure",
 * @endcode
 *
 * @DCG
 * For entities, it is recommended to use REST resource plugin provided by
 * Drupal core.
 * @see \Drupal\rest\Plugin\rest\resource\EntityResource
 */
final class RestAccommodation extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   *   The response containing accommodation data.
   */

  public function get(): ModifiedResourceResponse {
    //Hämtar uid för aktiv användare
    $current_user_id = \Drupal::currentUser()->id();

    // hämtar accommodation noder som är skapade av aktiv användare
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'accommodation')
      ->condition('uid', $current_user_id)
      ->accessCheck(TRUE)
      ->execute();

    //skicka förfrågan och tar emot flera noder av bostäder
    $nodes = \Drupal\node\Entity\Node::loadMultiple($query);

    //Deklarerar en array för att lagra all retur data i
    $data = [];

    // Loopar igenom alla hämtade noder samt tilldelar ett objekt med relevant data för varje node
    foreach ($nodes as $node) {
      $data[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        'emailaddress' => $node->get('field_emailadress')->value,
        'rent' => $node->get('field_rent')->value,
        'tenant' => $node->get('field_tenant')->value,
        'real_estate_id' => $node->get('field_realestateid')->getValue(),
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

    // Kontrollerar så att tile samt real_estate_id skickades med post-anropet
    if (empty($data['title'])) {
      return new ModifiedResourceResponse(['error' => 'Title is required.'], 406);
    }
    if (empty($data['real_estate_id'])) {
      return new ModifiedResourceResponse(['error' => 'real estate id is required.'], 406);
    }

    // deklarerar data och skapar en bostads node
    $node = \Drupal\node\Entity\Node::create([
      'type' => 'accommodation',
      'title' => $data['title'],
      'field_emailadress' => $data['emailaddress'],
      'field_realestateid' => $data['real_estate_id'],
      'field_rent' => $data['rent'] ?? 0,
      'field_tenant' => $data['tenant'],
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
    // Hämta noden med det angivna ID:t (html-parameter)
    $node = $this->getNodeById($id);

    //Hämtar uid för aktiva användaren
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
        // Noden tillhör inte användaren, returerar exception
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('You do not have access to this node.');
      }
    }
    else {
      // Returnera ett fel om noden inte hittades
      return new ModifiedResourceResponse(['error' => 'Node not found'], 404); // 404 Not Found
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
    // Hämta noden med det angivna ID:t (html-parameter)
    $node = $this->getNodeById($id);

    //hämtar aktiva användaren uid
    $current_user_id = \Drupal::currentUser()->id();

    // Kontrollera om noden finns
    if ($node) {
      // Kontrollera om noden tillhör den inloggade användaren
      if ($node->getOwnerId() === $current_user_id) {
        // Uppdatera nodens fält om de är medskickade
        if (isset($data['title'])) {
          $node->setTitle($data['title']);
        }
        if (isset($data['real_estate_id'])) {
          $node->set('field_realestateid', $data['real_estate_id']);
        }
        if (isset($data['emailaddress'])) {
          $node->set('field_emailadress', $data['emailaddress']);
        }
        if (isset($data['rent'])) {
          $node->set('field_rent', $data['rent']);
        }
        if (isset($data['tenant'])) {
          $node->set('field_tenant', $data['tenant']);
        }

        // Spara noden
        $node->save();

        // Returnerar ändrat object
        return new ModifiedResourceResponse($node, 201);
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

  // En metod för att hämta noden baserat på ID
  private function getNodeById($nodeId) {
    // Här hämtas en nod från id parameter
    return \Drupal\node\Entity\Node::load($nodeId);
  }

}
