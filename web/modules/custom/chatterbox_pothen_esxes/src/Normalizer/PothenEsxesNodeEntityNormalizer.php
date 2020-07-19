<?php

  namespace Drupal\chatterbox_pothen_esxes\Normalizer;

  use Drupal\serialization\Normalizer\ContentEntityNormalizer;
  use Drupal\node\NodeInterface;

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
    $title = $entity->getTitle();
    var_dump ($entity->field_political_affiliation->first());exit;
    $politicalAffiliation = $entity->field_political_affiliation->first()->getValue();

    $new_attributes['metadata'] = array(
      'title' => $title,
      'politicalAffiliation' => $politicalAffiliation
    );

    // Return the $attributes with our new values.
    return $new_attributes;
  }
}