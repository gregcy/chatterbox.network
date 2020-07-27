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
    if (!is_null($entity->field_political_affiliation->first())) {
      $politicalAffiliation_tid = $entity->field_political_affiliation->first()->getValue()['target_id'];
      $politicalAffiliation = Term::load($politicalAffiliation_tid);
      $politicalAffiliation = $politicalAffiliation->getName();
    }
    if (!is_null($entity->field_biography->first())) {
      $biographyLink = $entity->field_biography->first()->getValue()['uri'];
    }
    if (!is_null($entity->field_politician_image->first())) {
      $media_id = $entity->field_politician_image->first()->getValue()['target_id'];
      $media = Media::load($media_id);
      $fid = $media->field_media_image->target_id;
      $file = File::Load($fid);
      $imageURL = $file->url();
    }
    if (!is_null($entity->field_wikidata_entity_id)){
      $wikimediaId = $entity->field_wikidata_entity_id->getValue()[0]['value'];      
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
    if (!is_null($entity->field_name_and_surname)) {
      $personalDataName = $entity->field_name_and_surname->getValue()[0]['value'];
    }
    $personalData['name'] = array('label' => 'Ονοματεπώνυμο', 'value' => $personalDataName);
    // Office
    if (!is_null($entity->field_office)) {
      $personalDataOffice = $entity->field_office->getValue()[0]['value'];
    }
    $personalData['office'] = array('label' => 'Ιδιότητα - Αξίωμα', 'value' => $personalDataOffice);
    // Home Address
    if(sizeof($entity->field_home_address->getValue())> 0) {
      $perosnalDataAddress = $entity->field_home_address->getValue()[0]['value'];
    }
    $personalData['addressHome'] = array('label' => 'Διεύθυνση κατοικίας', 'value' => $perosnalDataAddress);
    // Date of Birth
    if (sizeof($entity->field_dob->getValue()) > 0 ) {
      $personalDOB = $entity->field_dob->getValue()[0]['value'];
      $personalDOBMeta = $entity->field_meta_dob->getValue()[0]['value'];
      $personalDOBMeta = DrupalDateTime::createFromFormat('Y-m-d',$personalDOBMeta, null);
      $personalDOBMeta = \Drupal::service('date.formatter')->format($personalDOBMeta->getTimestamp(), 'html_datetime');
    }
    $personalData['DOB'] = array('label' => 'Ημερομηνία γεννήσεως', 'value' => $personalDOB, 'metaValue' => $personalDOBMeta);
    // ID Number
    $personalData['id'] = array('label' => 'Αριθμός Ταυτότητας', 'value' => '');
    // Marital Status
    if (!is_null($entity->field_married)) {
      $personalMarried = $entity->field_married->getValue()[0]['value'];
      $personalMarriedMeta = $entity->field_married_meta->getValue()[0]['value'];
      if ($personalMarriedMeta == 1 ) {
        $personalMarriedMeta = 'Έγγαμος';
      }
      else if ($personalMarriedMeta == 0) {
        $personalMarriedMeta = 'Άγαμος';
      }
    }
    $personalData['maritalStatus'] = array('label' => 'Έγγαμος/Άγαμος', 'value' => $personalMarried, 'metaValue' => $personalMarriedMeta);
    // Number of Dependants
    if (!is_null($entity->field_number_of_dependents)) {
      $personalNoOfDependants = $entity->field_number_of_dependents->getValue()[0]['value'];
      $personalNoOfDependantsMeta = (int) $entity->field_number_of_dependents_meta->getValue()[0]['value'];
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