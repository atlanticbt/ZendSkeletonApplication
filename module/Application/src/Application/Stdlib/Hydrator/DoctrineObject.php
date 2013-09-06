<?php

namespace Application\Stdlib\Hydrator;

use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Reflection\ClassReflection;

/**
 * @author John Foushee <john.foushee@atlanticbt.com>
 *
 * @TODO: need to map the property name to the form field name. In most cases
 * this is the same value (say $firstName => $formData['firstName']) however
 * if the form is annotated so the $firstName property has a form annotation
 * specified name of 'userFirst', the value no longer gets mapped.
 *
 */
class DoctrineObject extends \DoctrineModule\Stdlib\Hydrator\DoctrineObject
{

}
