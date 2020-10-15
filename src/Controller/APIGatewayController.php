<?php

namespace Drupal\api_publisher\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\api_publisher\Entity\APIGatewayInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class APIGatewayController.
 *
 *  Returns responses for APIGateway routes.
 */
class APIGatewayController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a APIGateway revision.
   *
   * @param int $api_gateway_revision
   *   The APIGateway revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($api_gateway_revision) {
    $api_gateway = $this->entityTypeManager()->getStorage('api_gateway')
      ->loadRevision($api_gateway_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('api_gateway');

    return $view_builder->view($api_gateway);
  }

  /**
   * Page title callback for a APIGateway revision.
   *
   * @param int $api_gateway_revision
   *   The APIGateway revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($api_gateway_revision) {
    $api_gateway = $this->entityTypeManager()->getStorage('api_gateway')
      ->loadRevision($api_gateway_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $api_gateway->label(),
      '%date' => $this->dateFormatter->format($api_gateway->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a APIGateway.
   *
   * @param \Drupal\api_publisher\Entity\APIGatewayInterface $api_gateway
   *   A APIGateway object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(APIGatewayInterface $api_gateway) {
    $account = $this->currentUser();
    $api_gateway_storage = $this->entityTypeManager()->getStorage('api_gateway');

    $build['#title'] = $this->t('Revisions for %title', ['%title' => $api_gateway->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all apigateway revisions") || $account->hasPermission('administer apigateway entities')));
    $delete_permission = (($account->hasPermission("delete all apigateway revisions") || $account->hasPermission('administer apigateway entities')));

    $rows = [];

    $vids = $api_gateway_storage->revisionIds($api_gateway);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\api_publisher\APIGatewayInterface $revision */
      $revision = $api_gateway_storage->loadRevision($vid);
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $api_gateway->getRevisionId()) {
          $link = $this->l($date, new Url('entity.api_gateway.revision', [
            'api_gateway' => $api_gateway->id(),
            'api_gateway_revision' => $vid,
          ]));
        }
        else {
          $link = $api_gateway->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => Url::fromRoute('entity.api_gateway.revision_revert', [
                'api_gateway' => $api_gateway->id(),
                'api_gateway_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.api_gateway.revision_delete', [
                'api_gateway' => $api_gateway->id(),
                'api_gateway_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
    }

    $build['api_gateway_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
