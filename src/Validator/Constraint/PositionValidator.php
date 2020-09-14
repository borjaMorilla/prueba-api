<?php


namespace App\Validator;


use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PositionValidator extends ConstraintValidator
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Player $object
     * @param Constraint $constraint
     */
    public function validate($object, Constraint $constraint)
    {
        dd($object);
    }
}