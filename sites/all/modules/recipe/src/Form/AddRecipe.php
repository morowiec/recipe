<?php

namespace Drupal\recipe\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Defines a form that allows privileged users to add Recipe.
 */
class AddRecipe extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_recipe';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
    );
    $form['field_author_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Author name'),
    );
    $form['field_author_email'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Author email'),
    );
    $form['field_description'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#description' => $this->t('Max 500 characters'),
    );
    $form['field_ingredients'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Ingredients'),
    );
    $form['field_instructions'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Instructions'),
    );
    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit recipe'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('field_description')) > 500) {
      $form_state->setErrorByName('field_description', $this->t('Description too long.'));
    }

    // Email validation
    if (\Drupal::service('email.validator')->isValid($form_state->getValue('field_author_email')) != 1) {
      $form_state->setErrorByName('field_author_email', $this->t('Author email invalid.'));
    }
    if (strlen($form_state->getValue('field_author_email')) < 3) {
      $form_state->setErrorByName('field_author_email', $this->t('Author email too short.'));
    }
    
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity_type = "node";
    $bundle = "recipe";

    $entity_def = \Drupal::entityManager()->getDefinition($entity_type);

    $new_recipe = array(
      'title' => $form_state->getValue('title'),
      'field_author_name' => $form_state->getValue('field_author_name'),
      'field_author_email' => $form_state->getValue('field_author_email'),
      'field_description' => $form_state->getValue('field_description'),
      'field_ingredients' => $form_state->getValue('field_ingredients'),
      'field_instructions' => $form_state->getValue('field_instructions'),
      $entity_def->get('entity_keys')['bundle'] => $bundle
    );

    $node = \Drupal::entityManager()->getStorage($entity_type)->create($new_recipe);
    $node->save();
    pathauto_entity_insert($node);

    drupal_set_message($this->t('Your recipe was added correctly'));
  }

}
