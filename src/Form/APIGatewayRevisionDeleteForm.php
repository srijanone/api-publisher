<?php

namespace Drupal\api_publisher\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a APIGateway revision.
 *
 * @ingroup api_publisher
 */
class APIGatewayRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The APIGateway revision.
   *
   * @var \Drupal\api_publisher\Entity\APIGatewayInterface
   */
  protected $revision;

  /**
   * The APIGateway storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $aPIGatewayStorage;

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
    $instance->aPIGatewayStorage = $container->get('entity_type.manager')->getStorage('api_gateway');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'api_gateway_revision_delete_confirm';
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
    return new Url('entity.api_gateway.version_history', ['api_gateway' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $api_gateway_revision = NULL) {
    $this->revision = $this->APIGatewayStorage->loadRevision($api_gateway_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->APIGatewayStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('APIGateway: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of APIGateway %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.api_gateway.canonical',
       ['api_gateway' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {api_gateway_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.api_gateway.version_history',
         ['api_gateway' => $this->revision->id()]
      );
    }
  }

}
