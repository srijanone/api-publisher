<?php

namespace Drupal\api_publisher\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\api_publisher\Entity\ProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ProxyController.
 *
 *  Returns responses for Proxy routes.
 */
class ProxyController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Proxy revision.
   *
   * @param int $proxy_revision
   *   The Proxy revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($proxy_revision) {
    $proxy = $this->entityTypeManager()->getStorage('proxy')
      ->loadRevision($proxy_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('proxy');

    return $view_builder->view($proxy);
  }

  /**
   * Page title callback for a Proxy revision.
   *
   * @param int $proxy_revision
   *   The Proxy revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($proxy_revision) {
    $proxy = $this->entityTypeManager()->getStorage('proxy')
      ->loadRevision($proxy_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $proxy->label(),
      '%date' => $this->dateFormatter->format($proxy->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Proxy.
   *
   * @param \Drupal\api_publisher\Entity\ProxyInterface $proxy
   *   A Proxy object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ProxyInterface $proxy) {
    $account = $this->currentUser();
    $proxy_storage = $this->entityTypeManager()->getStorage('proxy');

    $build['#title'] = $this->t('Revisions for %title', ['%title' => $proxy->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all proxy revisions") || $account->hasPermission('administer proxy entities')));
    $delete_permission = (($account->hasPermission("delete all proxy revisions") || $account->hasPermission('administer proxy entities')));

    $rows = [];

    $vids = $proxy_storage->revisionIds($proxy);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\api_publisher\ProxyInterface $revision */
      $revision = $proxy_storage->loadRevision($vid);
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $proxy->getRevisionId()) {
          $link = $this->l($date, new Url('entity.proxy.revision', [
            'proxy' => $proxy->id(),
            'proxy_revision' => $vid,
          ]));
        }
        else {
          $link = $proxy->link($date);
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
              'url' => Url::fromRoute('entity.proxy.revision_revert', [
                'proxy' => $proxy->id(),
                'proxy_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.proxy.revision_delete', [
                'proxy' => $proxy->id(),
                'proxy_revision' => $vid,
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

    $build['proxy_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
