<?php
    namespace Drupal\chatterbox_pothen_esxes\Normalizer;
    
    use Drupal\serialization\Normalizer\ContentEntityNormalizer;
    use Drupal\node\NodeInterface;
    
    class MyNormalizerClass extends ContentEntityNormalizer {
        /**
        * The interface or class that this Normalizer supports.
        * * @var string 
        */
        protected $supportedInterfaceOrClass = 'Drupal\node\NodeInterface';
        /** 
        * * {@inheritdoc} 
        */
        public function supportsNormalization($data, $format = NULL) {
            // If we aren't dealing with an object or the format is not supported return// now.
            if (!is_object($data) || !$this->checkFormat($format)) {
            return FALSE;
            }
            // This custom normalizer should be supported for "Pothen Esxes" nodes.
            if ($data instanceof NodeInterface && $data->getType() == 'pothen-esxes') {
            return TRUE;
            }
            // Otherwise, this normalizer does not support the $data object.
            return FALSE;
        }
        
        /**
        * {@inheritdoc}
        */
        public function normalize($entity, $format = NULL, array $context = array()) {
            $attributes = parent::normalize($entity, $format, $context);
            print($attributes);exit;
            // Return the $attributes with our new value.
            return $attributes;
        }
    }