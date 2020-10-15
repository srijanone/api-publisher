<?php

namespace Drupal\api_publisher\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a OpenAPI Specification revision.
 *
 * @ingroup api_publisher
 */
class OpenAPISpecRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The OpenAPI Specification revision.
   *
   * @var \Drupal\api_publisher\Entity\OpenAPISpecInterface
   */
  protected $revision;

  /**
   * The OpenAPI Specification storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $openAPISpecStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->openAPISpecStorage = $container->get('entity_type.manager')->getStorage('open_api_spec');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'open_api_spec_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.open_api_spec.version_history', ['open_api_spec' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $open_api_spec_revision = NULL) {
    $this->revision = $this->OpenAPISpecStorage->loadRevision($open_api_spec_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->OpenAPISpecStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('OpenAPI Specification: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of OpenAPI Specification %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.open_api_spec.canonical',
       ['open_api_spec' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {open_api_spec_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.open_api_spec.version_history',
         ['open_api_spec' => $this->revision->id()]
      );
    }
  }

}
