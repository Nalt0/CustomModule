<?php

namespace Drupal\custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CollectEmail extends FormBase {

  public function getFormId() {
    return 'collect_email';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

   
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Your name')
    );
	
	$form['lastname'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Your last name')
    );
	
	$form['subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Subject')
    );
	
	$form['message'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Your message')
    );
	
	$form['email'] = array(
      '#type' => 'email',
      '#title' => $this->t('Email')
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Send mail'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('email')) == null) {
      $form_state->setErrorByName('email', $this->t('Email is null.'));
    }
	
	if (!preg_match('/@.+\./', $form_state->getValue('email'))){
      $form_state->setErrorByName('email', $this->t('Email is not valid.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    drupal_set_message($this->t('Your message has been send.', array(
      '@number' => $form_state->getValue('email')
	  )));

    $email = $form_state->getValue('email');
	$firstname = $form_state->getValue('name');
	$lastname = $form_state->getValue('lastname');


	$endpoint = "https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/".$email."/?hapikey=ee216612-1dec-45b6-aa72-a61af2edc2f4";

	$data = array(
	'properties' => [
	[
		'property' => 'firstname',
		'value' => $firstname
	],
	[
		'property' => 'lastname',
		'value' => $lastname
	]
	]
	);

    $post_json = json_encode($data,true);
	@curl_setopt($ch, CURLOPT_POST, true);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
	@curl_setopt($ch, CURLOPT_URL, $endpoint);
	@curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = @curl_exec($ch);
	$status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curl_errors = curl_error($ch);
	@curl_close($ch);

		// 		$query = \Drupal::database()->insert('mail');
		// 		$query->fields([
		// 		'message' => $form_state->getValue('message'),
		// 		'email' => $form_state->getValue('email'),
		// 		'subject' => $form_state->getValue('subject'),
		// 		]);
		// 		$query->execute();

  }

}	