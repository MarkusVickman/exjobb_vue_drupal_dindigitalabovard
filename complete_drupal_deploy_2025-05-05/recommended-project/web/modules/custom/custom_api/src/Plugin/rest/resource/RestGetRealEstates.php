<?php

declare(strict_types=1);

namespace Drupal\custom_api\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\user\Entity\User;

/**
 * Represents positions as resources.
 *
 * @RestResource(
 *   id = "rest_get_real_estates",
 *   label = @Translation("Real estate listing Custom REST API"),
 *   uri_paths = {
 *     "canonical" = "api/realestate_list"
 *   }
 * )
 */
final class RestGetRealEstates extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   *   The response containing real estate data. Is used for none verified
   * users to get a list on realestates
   */

  public function get(): ModifiedResourceResponse {
    // databas fråga för att hämta fastighet
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'realestate')
      ->accessCheck(FALSE)
      ->execute();

    //kör fråga
    $nodes = \Drupal\node\Entity\Node::loadMultiple($query);

    //Data array som ska returnera data
    $data = [];

    // Loopar igenom alla hämtade object
    foreach ($nodes as $node) {
      // Hämta användarens ID från noden
      $user_id = $node->get('uid')->target_id;

      if (!$user_id) {
        continue;
      }

      // Laddar användarobjektet med användarens ID
      $user = User::load($user_id);

      // Hämtar användarnamnet
      $username = $user->getAccountName();

      $data[] = [
        'id' => $node->id(),
        'title' => $username .' - '. $node->getTitle(),
      ];
    }

    return new ModifiedResourceResponse($data, 200);
  }

}
