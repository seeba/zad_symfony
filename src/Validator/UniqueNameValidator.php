<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        
        if (!$constraint instanceof UniqueName) {
            throw new UnexpectedTypeException($constraint, UniqueName::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $names = [];
        foreach ($value as $element) {
            if (in_array($element->getName(), $names, true)) {
                $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $element->getName())
                ->addViolation();
            }
            $names[] = $element->getName();
        }        
    }
}
