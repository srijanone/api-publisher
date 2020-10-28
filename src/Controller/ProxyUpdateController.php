<?php

namespace Drupal\api_publisher\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use TheRealGambo\Kong\Kong;
use Drupal\api_publisher\Entity\Proxy;
use Drupal\api_publisher\Entity\APIGateway;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ProxyUpdateController.
 *
 *  Returns Boolean.
 */
class ProxyUpdateController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Update snapshot with the updated openapi specification.
   *
   * @param int $entityId
   *   The enity id.
   *
   * @return string
   *   The update status.
   */
  public function updateSnapshot($entity_id) {
    $response = new AjaxResponse();
    $entity = $this->entityTypeManager()->getStorage('proxy')
      ->load($entity_id);
    $openApiSpec = $entity->get('openapi_spec')->entity;
    if ($entity->snapshot->value != base64_encode($openApiSpec->openapi_spec->value)) {
      $entity->snapshot->value = base64_encode($openApiSpec->openapi_spec->value);
      $entity->save();
      // Proxy snapshot on Apigateway.
      $updateProxy = $this->updateProxy($entity, $openApiSpec);
      // Replace HTML markup inside the div via a selector.
      $text = '<i class="fa fa-check" aria-hidden="true"></i>';
      $selector = '#sync-status-' . $entity_id;
      $class = '.sync-snapshot-' . $entity_id;
      $response->addCommand(new ReplaceCommand($selector, $text));
      $response->addCommand(new RemoveCommand($class));
    }
    return $response;
  }

  /**
   * Update snapshot with the updated openapi specification.
   *
   * @param object $entity
   *   The entity.
   *
   * @return string
   *   The update status.
   */
  public function updateProxy($entity, $openApiSpec) {
    if ($entity instanceof Proxy) {
      $apiGateway = $entity->get('api_gateway')->entity;
      
      // Fetch kong admin API url.
      $kongUrl = $this->createUrl($apiGateway);
      
      // Fetch openAPI specification from snapshot.
      $spec = $this->getSpecification($entity);

      // get baseurl.
      $baseUrl = $this->getBasePath($spec);

      // get kong application port.
      $kongPort = $apiGateway->get('port')->value;

      // Create a service on kong API gateway.
      return $this->createService($kongUrl, $kongPort, $baseUrl, $spec);
      // Register the proxy.
      //$route = $this->createRoute($kongUrl, $kongPort, $services);
    }
  }

  /**
   * Generate Kong admin API url.
   *
   * @param object $entity
   *   The entity.
   * 
   * @return string
   *   The kong admin API url.
   */
  public function createUrl($apiGateway) {
    $host = $apiGateway->get('hostname')->value;
    $protocol = $apiGateway->get('protocol')->value;

    return $protocol . '://' . $host;
  }

  /**
   * Return API Specification from snapshot.
   *
   * @param object $entity
   *   The entity.
   *
   * @return array
   *   The API specification.
   */
  public function getSpecification($entity) {
    $snapshot = base64_decode($entity->get('snapshot')->value);
    $output = Yaml::parse($snapshot);
    return $output;
  }

  /**
   * Return basepath.
   *
   * @param array $spec
   *   The openapi sepecification.
   *
   * @return string
   *   The basePath.
   */
  public function getBasePath($spec) {
    // Hardcoded set host.
    $spec['host'] = 'drupal8_drupal:8080';
    // Default to http
    $scheme = isset($spec['schemes'][0]) ? $spec['schemes'][0] : 'http'; 
    $baseUrl = $scheme . '://' . $spec['host'] . $spec['basePath'];
    return $baseUrl;
  }

  /**
   * Creates service on API Gateway
   *
   * @param string $url
   *   The API gateway connection URl.
   * @param int $port
   *   The API gateway port.
   * @param string $baseUrl
   *   The baseurl for creating a service.
   * @param array $spec
   *   The Parsed sepcification data.
   *
   * @return array
   *   The Services ids.
   */
  public function createService($url, $port, $baseUrl, $spec) {
    $kong = new Kong($url, $port);
    $serviceObj = $kong->getServiceObject();
    $routes = [];
    if(isset($spec['paths'])) {
      foreach($spec['paths'] as $path => $data) {
        $name = str_replace('/', '--', ltrim($path, '/'));
        $serviceObj->add([
          'name' => $name,
          'url' => $baseUrl . $path
        ]);
        $response = $serviceObj->getResponse();
        $service = $response->body['id'];
        // Register routes for newly created service.
        $routes = $this->createRoute($url, $port, $name, $path, $service, $data);
      }
    }
    return $routes;
  }

  /**
   * Creates routes for service.
   *
   * @param string $url
   *   The API gateway connection URl.
   * @param int $port
   *   The API gateway port.
   * @param string $service
   *   The service id.
   * @param array $data
   *   The specification data.
   *
   * @return array
   *   The Services ids.
   */
  public function createRoute($url, $port, $name, $path, $service, $data) {
    $kong = new Kong($url, $port);
    $routeObj = $kong->getRouteObject();
    $routeIds = [];
    foreach($data as $method => $value) {
      $methods[] = strtoupper($method);
    }
    $routeObj->add([
      'name' => $name,
      'service' => [
        'id' => $service,
      ],
      'paths' => [
        '/api/v1' . $path,
      ],
      'methods' => $methods,
    ]);
    $response = $routeObj->getResponse();
    $routeIds[] = $response->body['id'];

    return $routeIds;
  }

}
