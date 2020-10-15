<?php

namespace Drupal\api_publisher\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\api_publisher\Entity\OpenAPISpecInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OpenAPISpecController.
 *
 *  Returns responses for OpenAPI Specification routes.
 */
class OpenAPISpecController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a OpenAPI Specification revision.
   *
   * @param int $open_api_spec_revision
   *   The OpenAPI Specification revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($open_api_spec_revision) {
    $open_api_spec = $this->entityTypeManager()->getStorage('open_api_spec')
      ->loadRevision($open_api_spec_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('open_api_spec');

    return $view_builder->view($open_api_spec);
  }

  /**
   * Page title callback for a OpenAPI Specification revision.
   *
   * @param int $open_api_spec_revision
   *   The OpenAPI Specification revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($open_api_spec_revision) {
    $open_api_spec = $this->entityTypeManager()->getStorage('open_api_spec')
      ->loadRevision($open_api_spec_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $open_api_spec->label(),
      '%date' => $this->dateFormatter->format($open_api_spec->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a OpenAPI Specification.
   *
   * @param \Drupal\api_publisher\Entity\OpenAPISpecInterface $open_api_spec
   *   A OpenAPI Specification object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(OpenAPISpecInterface $open_api_spec) {
    $account = $this->currentUser();
    $open_api_spec_storage = $this->entityTypeManager()->getStorage('open_api_spec');

    $build['#title'] = $this->t('Revisions for %title', ['%title' => $open_api_spec->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all openapi specification revisions") || $account->hasPermission('administer openapi specification entities')));
    $delete_permission = (($account->hasPermission("delete all openapi specification revisions") || $account->hasPermission('administer openapi specification entities')));

    $rows = [];

    $vids = $open_api_spec_storage->revisionIds($open_api_spec);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\api_publisher\OpenAPISpecInterface $revision */
      $revision = $open_api_spec_storage->loadRevision($vid);
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $open_api_spec->getRevisionId()) {
          $link = $this->l($date, new Url('entity.open_api_spec.revision', [
            'open_api_spec' => $open_api_spec->id(),
            'open_api_spec_revision' => $vid,
          ]));
        }
        else {
          $link = $open_api_spec->link($date);
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
              'url' => Url::fromRoute('entity.open_api_spec.revision_revert', [
                'open_api_spec' => $open_api_spec->id(),
                'open_api_spec_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.open_api_spec.revision_delete', [
                'open_api_spec' => $open_api_spec->id(),
                'open_api_spec_revision' => $vid,
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

    $build['open_api_spec_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
