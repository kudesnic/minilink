<?php

namespace App\Validator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValueInEntityValidator extends ConstraintValidator
{

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        $entityRepository = $this->em->getRepository($constraint->entityClass);

        if (!is_scalar($constraint->field)) {
            throw new InvalidArgumentException('"field" parameter should be any scalar type');
        }

        $searchResults = $entityRepository->findBy([
            $constraint->field => $value
        ]);

        if (count($searchResults) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
