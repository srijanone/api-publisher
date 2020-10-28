<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines a class to build a listing of Proxy entities.
 *
 * @ingroup api_publisher
 */
class ProxyListBuilder extends EntityListBuilder {

  /**
   * The user entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $specEntity;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Proxy ID');
    $header['name'] = $this->t('Name');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\api_publisher\Entity\Proxy $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.proxy.edit_form',
      ['proxy' => $entity->id()]
    );
    // Snapshot status.
    $specId = $entity->openapi_spec->target_id;
    $openApiSpec = \Drupal::entityTypeManager()->getStorage('open_api_spec')->load($specId);
    if ($entity->snapshot->value == base64_encode($openApiSpec->openapi_spec->value)) {
      // @todo We could add the template for html content.
      $row['status'] =  new FormattableMarkup('<div id="sync-status-' . $entity->id() . '"><i class="fa fa-check" aria-hidden="true"></i></div>', []);
    }
    else {
      // @todo We could add the template for html content.
      $row['status'] =  new FormattableMarkup('<div id="sync-status-' . $entity->id() . '"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></div>', []);
    }
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function buildOperations(EntityInterface $entity) {
    $id = $entity->id();
    $specId = $entity->openapi_spec->target_id;
    $openApiSpec = \Drupal::entityTypeManager()->getStorage('open_api_spec')->load($specId);
    if ($entity->snapshot->value != base64_encode($openApiSpec->openapi_spec->value)) {
      $operations['sync_snapshot'] = [
        'title' => $this->t('Sync'),
        'weight' => 100,
        'url' => Url::fromRoute('api_publisher.update_snapshot', ['entity_id' => $entity->id()]),
        'attributes' => [
          'class' => ['use-ajax', 'sync-snapshot-' . $id],
          'progress' => [
            'type' => 'throbber',
            'message' => $this->t('Synching data...'),
          ],
        ],
      ];
      $operations = parent::getOperations($entity) + $operations;
    }
    else {
      $operations = parent::getOperations($entity);
    }
    $build = [
      '#type' => 'operations',
      '#links' => $operations,
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    // Attach fontawesome library.
    $build['#attached']['library'][] = 'api_publisher/api_publisher.webfonts';
    return $build;
  }

}
