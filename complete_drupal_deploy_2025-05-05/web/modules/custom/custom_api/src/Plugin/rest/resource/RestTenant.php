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
 *   id = "rest_tenant",
 *   label = @Translation("Tenant Custom REST API"),
 *   uri_paths = {
 *     "canonical" = "api/tenant/{email}",
 *      "create" = "api/tenant"
 *   }
 * )
 */
final class RestTenant extends ResourceBase {


  /**
   * Responds to GET requests.
   *
   *   The response containing a list of accommodation names and id for the tenant.
   */

  public function get($email): ModifiedResourceResponse {
    //Denna path används för att hämta en lista med accommodations där den boende är registrerad då den kan stå på flera lägenheter
    // databas fråga för att hämta faktura
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'accommodation')
      ->condition('field_emailadress', $email)
      ->accessCheck(FALSE)
      ->execute();

    // kör fråga
    $nodes = \Drupal\node\Entity\Node::loadMultiple($query);

    //Array för svarsdata
    $data = [];

    // Loopar igenom alla hämtade object
    foreach ($nodes as $node) {
      $data[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
      ];
    }

    return new ModifiedResourceResponse($data, 200);
  }

  /**
   * Responds to POST requests.
   *
   *   The response containing information relevant to the residents email address.
   */

  public function post(array $data): ModifiedResourceResponse {

    //kontrollerar om required data finns
    if (empty($data['email'])) {
      return new ModifiedResourceResponse(['error' => 'Email is required.'], 406);
    }

    if (empty($data['realEstateId'])) {
      return new ModifiedResourceResponse(['error' => 'Real Estate Id is required.'], 406);
    }

    //Deklarerar email och REID
    $email = $data['email'];
    $realEstateId = $data['realEstateId'];

    // databas fråga för att hämta information för specifik email
    $queryAcc = \Drupal::entityQuery('node')
      ->condition('type', 'accommodation')
      ->condition('field_realestateid', $realEstateId)
      ->condition('field_emailadress', $email)
      ->accessCheck(False)
      ->execute();

    //kör fråga
    $nodesAcc = \Drupal\node\Entity\Node::loadMultiple($queryAcc);

    // om emailen inte finns registrerad på något boende
    if(!$nodesAcc > 0 || empty($nodesAcc)){
      return new ModifiedResourceResponse(['error' => 'The Email address is not registered for selected living host.'], 406);
    }

    // databas-fråga för att hämta information för fastigheten
    $queryInfo = \Drupal::entityQuery('node')
      ->condition('type', 'information')
      ->condition('field_realestate_id', $realEstateId)
      ->accessCheck(False)
      ->execute();

    //fråga körs
    $nodesInfo = \Drupal\node\Entity\Node::loadMultiple($queryInfo);

    //Om inte info finns skickas felmeddelande
    if(empty($nodesInfo)) {
      return new ModifiedResourceResponse(null, 204);
    }

    //Laddar noden för den valda fastigheten och hämtar sedan dess titel
    $realestate_node = \Drupal\node\Entity\Node::load($realEstateId);
    $realestate_title = $realestate_node->getTitle();

    //array för att lagra object till retur
    $response = [];

    // Loopar igenom alla hämtade object
    foreach ($nodesInfo as $node) {

      $response[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        'information' => $node->get('field_information')->value,
        'realestate_id' => $node->get('field_realestate_id')->getValue(),
        'realestate_title' => $realestate_title,
        'created' => $node->getCreatedTime(),
      ];
    }
    return new ModifiedResourceResponse($response, 200);
  }
}
