<?php

  namespace Drupal\chatterbox_pothen_esxes\Normalizer;

  use Drupal\serialization\Normalizer\ContentEntityNormalizer;
  use Drupal\node\NodeInterface;
  use Drupal\taxonomy\Entity\Term;
  use Drupal\media\Entity\Media;
  use Drupal\file\Entity\File;
  use Drupal\Core\Datetime\DrupalDateTime;
  use Drupal\Core\Datetime\DateFormatter;

/** 
* Converts the Drupal entity object structures to a normalized array. 
*/
class PothenEsxesNodeEntityNormalizer extends ContentEntityNormalizer {
  /** 
  * The interface or class that this Normalizer supports. 
  *  @var string 
  */
  protected $supportedInterfaceOrClass = 'Drupal\node\NodeInterface';
   
  public function supportsNormalization($data, $format = NULL) {
    // If we aren't dealing with an object or the format is not supported return now.
    if (!is_object($data) || !$this->checkFormat($format)) {
      return FALSE;
    }
    // This custom normalizer should be supported for "Pothen Esxes" nodes.
    if ($data instanceof NodeInterface && $data->getType() == 'pothen_esxes') {
      return TRUE;
    }
    // Otherwise, this normalizer does not support the $data object.
      return FALSE;
  }
    
  /**
  * {@inheritdoc} 
  */
  public function normalize($entity, $format = NULL, array $context = array()) {
       
    $new_attributes = array();
    
    // Metadata Element
    $metadata = array();
    $politicalAffiliation = '';
    $biographyLink = '';
    $imageURL = '';
    $wikimediaId = '';
    
    $title = $entity->getTitle();
    // Political Affiliation
    $politicalAffiliation = $entity->field_political_affiliation->entity;
    if (is_null($politicalAffiliation)) {
      $politicalAffiliation = '';
    }
    else {
      $politicalAffiliation = $politicalAffiliation->getName();
    }
    // Biography
    $biographyLink = $entity->field_biography->first();
    if (is_null($biographyLink)) {
      $biographyLink = '';
    }
    else {
      $biographyLink = $biographyLink->getUrl()->getUri();
    }
    // Image
    $imageURL = $entity->field_politician_image->entity;
    if (is_null($imageURL)) {
      $imageURL = '';
    }
    else {
      $fid = $imageURL->field_media_image->target_id;
      $file = File::Load($fid);
      $imageURL = $file->url();
    }
    // Wikimedia Id
    $wikimediaId = $entity->get('field_wikidata_entity_id')->value;
    if (is_null($wikimediaId)) {
      $wikimediaId = '';     
    }
    $metadata['title'] = $title;
    $metadata['politicalAffiliation'] =$politicalAffiliation;
    $metadata['biographyLink'] = $biographyLink;
    $metadata['image'] = $imageURL;
    $metadata['wikimediaId'] = $wikimediaId;

    // Part A
    $partA = array();

    $personalData = array();
    $personalDataName = '';
    $personalDataOffice = '';
    $perosnalDataAddress = '';
    $personalDOB = '';
    $personalDOBMeta = '';
    $personalMarried = '';
    $personalMarriedMeta = '';
    $personalNoOfDependants = '';
    $personalNoOfDependantsMeta = '';
    $personaldateOfSubmission = '';
    $personaldateOfSubmissionMeta = '';

    $partA['label'] = "Μέρος Α'";
    $partA['order'] = 1;
    $partA['title'] ="ΠΡΟΣΩΠΙΚΑ ΣΤΟΙΧΕΙΑ ΠΡΟΕΔΡΟΥ, ΥΠΟΥΡΓΟΥ Ή ΒΟΥΛΕΥΤΗ ΤΗΣ ΚΥΠΡΙΑΚΗΣ ΔΗΜΟΚΡΑΤΙΑΣ";
    // Part A - Personal Data
    // Name and Surname
    $personalDataName = $entity->get('field_name_and_surname')->value;
    if (is_null($personalDataName)) {
      $personalDataName = '';
    }
    $personalData['name'] = array('label' => 'Ονοματεπώνυμο', 'value' => $personalDataName);
    // Office
    $personalDataOffice = $entity->get('field_office')->value;
    if (is_null($personalDataOffice)) {
      $personalDataOffice = '';
    }
    $personalData['office'] = array('label' => 'Ιδιότητα - Αξίωμα', 'value' => $personalDataOffice);
    // Home Address
    $perosnalDataAddress = $entity->get('field_home_address')->value;
    if (is_null($perosnalDataAddress)) {
      $perosnalDataAddress = '';
    }
    $personalData['addressHome'] = array('label' => 'Διεύθυνση κατοικίας', 'value' => $perosnalDataAddress);
    // Date of Birth
    $personalDOB = $entity->get('field_dob')->value;
    if(is_null($personalDOB)) {
      $personalDOB = '';
    }
    $personalDOBMeta = $entity->get('field_meta_dob')->value;
    if (is_null($personalDOBMeta)) {
      $personalDOBMeta = '';
    }
    else {
      $personalDOBMeta = DrupalDateTime::createFromFormat('Y-m-d H:i',$personalDOBMeta . ' 00:00', null);
      $personalDOBMeta = \Drupal::service('date.formatter')->format($personalDOBMeta->getTimestamp(), 'html_datetime');
    }
    $personalData['DOB'] = array('label' => 'Ημερομηνία γεννήσεως', 'value' => $personalDOB, 'metaValue' => $personalDOBMeta);
    // ID Number
    $personalData['id'] = array('label' => 'Αριθμός Ταυτότητας', 'value' => '');
    // Marital Status
    $personalMarried = $entity->get('field_married')->value;
    if (is_null($personalMarried)) {
      $personalMarried = '';
    }
    $personalMarriedMeta = $entity->get('field_married_meta')->value;
    if ($personalMarriedMeta == 1 ) {
      $personalMarriedMeta = 'Έγγαμος';
    }
    else if ($personalMarriedMeta == 0) {
      $personalMarriedMeta = 'Άγαμος';
    }
    $personalData['maritalStatus'] = array('label' => 'Έγγαμος/Άγαμος', 'value' => $personalMarried, 'metaValue' => $personalMarriedMeta);
    // Number of Dependants
    $personalNoOfDependants = $entity->get('field_number_of_dependents')->value;
    if (is_null($personalNoOfDependants)) {
      $personalNoOfDependants = '';
    }
    $personalNoOfDependantsMeta = (int) $entity->get('field_number_of_dependents_meta')->value;
    if (is_null($personalNoOfDependantsMeta)) {
      $personalNoOfDependantsMeta = '';
    }
    $personalData['noOfDependants'] = array('label' => 'Αριθμός ανήλικων τέκνων', 'value' => $personalNoOfDependants, 'metaValue'=> $personalNoOfDependantsMeta);
    // Date of Submission
    if (!is_null($entity->field_date_of_submission)) {
      $personaldateOfSubmission = $entity->field_date_of_submission->getValue()[0]['value'];
      $personaldateOfSubmissionMeta = $entity->field_date_of_submission_meta->getValue()[0]['value'];
      $personaldateOfSubmissionMeta = DrupalDateTime::createFromFormat('Y-m-d',$personaldateOfSubmissionMeta, null);
      $personaldateOfSubmissionMeta = \Drupal::service('date.formatter')->format($personaldateOfSubmissionMeta->getTimestamp(), 'html_datetime');
    }
    $personalData['dateOfSubmission'] = array('label' => 'Ημερομηνία', 'value' => $personaldateOfSubmission, 'metaValue' => $personaldateOfSubmissionMeta);
 
    $partA['personalData'] = $personalData;

    $new_attributes['metadata'] = $metadata;
    $new_attributes['part'] = array($partA);

    // Return the $attributes with our new values.
    return $new_attributes;
  }
}