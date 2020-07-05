<?php

namespace Drupal\chatterbox_pothen_esxes\Normalizer;

use Drupal\serialization\Normalizer\NormalizerBase;

/** 
 * Converts typed data objects to arrays. 
 */
class CustomTypedDataNormalizer extends NormalizerBase
{
    /**
     * The interface or class that this Normalizer supports.
     * * @var string 
     */
    protected $supportedInterfaceOrClass = 'Drupal\Core\TypedData\TypedDataInterface';

    /**   
     * {@inheritdoc} 
     */
    public function normalize($object, $format = NULL, array $context = array())
    {
        $value = $object->getValue();
        //if (isset($value[0]) && isset($value[0]['value'])) {
          //  $value = $value[0]['value'];
        //}
        return $value;
    }
}
