<?php


namespace App\Util\Violation;


use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ViolationInterface
{
    public function build(ConstraintViolationListInterface $violations);
}