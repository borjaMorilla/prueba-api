<?php


namespace App\Validator\Constraint;


use Symfony\Component\Validator\Constraint;

class Position extends Constraint
{
    public $message = 'La posición no existe';

}