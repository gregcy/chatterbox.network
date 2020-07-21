<?php

  namespace Drupal\chatterbox_pothen_esxes\Normalizer;

  use Drupal\serialization\Normalizer\ContentEntityNormalizer;
  use Drupal\node\NodeInterface;
  use Drupal\taxonomy\Entity\Term;
  use Drupal\media\Entity\Media;
  use Drupal\file\Entity\File;
  use Drupal\Core\Datetime;
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
    if (!is_null($entity->field_biography)) {
      $biographyLink = $entity->field_biography->first()->getValue()['uri'];
    }
    if (!is_null($entity->field_politician_image)) {
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

    $partA['label'] = "Μέρος Α'";
    $partA['order'] = 1;
    $partA['title'] ="ΠΡΟΣΩΠΙΚΑ ΣΤΟΙΧΕΙΑ ΠΡΟΕΔΡΟΥ, ΥΠΟΥΡΓΟΥ Ή ΒΟΥΛΕΥΤΗ ΤΗΣ ΚΥΠΡΙΑΚΗΣ ΔΗΜΟΚΡΑΤΙΑΣ";
    // Part A - Personal Data
    if (!is_null($entity->field_name_and_surname)) {
      $personalDataName = $entity->field_name_and_surname->getValue()[0]['value'];
      $personalData['name'] = array('label' => 'Ονοματεπώνυμο', 'value' => $personalDataName);
    }
    if (!is_null($entity->field_office)) {
      $personalDataOffice = $entity->field_office->getValue()[0]['value'];
      $personalData['office'] = array('label' => 'Ιδιότητα - Αξίωμα', 'value' => $personalDataOffice);
    }
    if(!is_null($entity->field_home_address)) {
      $perosnalDataAddress = $entity->field_home_address->getValue()[0]['value'];
      $personalData['addressHome'] = array('label' => 'Διεύθυνση κατοικίας', 'value' => $perosnalDataAddress);
    }
    if (!is_null($entity->field_dob)) {
      $personalDOB = $entity->field_dob->getValue()[0]['value'];
      $personalData['DOB'] = array('label' => 'Ημερομηνία γεννήσεως', 'value' => $personalDOB);
    }
    if (!is_null($entity->field_meta_dob)) {
      $personalDOBMeta = $entity->field_meta_dob;
      $personalDOBMeta = DateFormatter::format($personalDOBMeta->getTimestamp(), 'html_datetime');
      var_dump($personalDOBMeta);exit;

    $partA['personalData'] = $personalData;

    $new_attributes['metadata'] = $metadata;
    $new_attributes['part'] = array($partA);

    // Return the $attributes with our new values.
    return $new_attributes;
  }
}