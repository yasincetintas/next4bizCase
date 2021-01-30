<?php

namespace App\Util\Violation;

use App\Helper\TextHelper;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Violation implements ViolationInterface
{
    /**
     * @param ConstraintViolationListInterface $violations
     *
     * @return string
     */
    public function build(ConstraintViolationListInterface $violations)
    {
        $errors = [];
        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[TextHelper::makeSnakeCase($violation->getPropertyPath())] = $violation->getMessage();
        }

        return json_encode($this->buildMessages($errors));
    }

    /**
     * @param array $errors
     *
     * @return array
     */
    private function buildMessages(array $errors): array
    {
        $result = [];

        foreach ($errors as $path => $message) {
            $temp = &$result;

            foreach (explode('.', $path) as $key) {
                preg_match('/(.*)(\[.*?\])/', $key, $matches);
                if ($matches) {
                    $index = str_replace(['[', ']'], '', $matches[2]);
                    $temp = &$temp[$matches[1]][$index];
                } else {
                    $temp = &$temp[$key];
                }
            }

            $temp = $message;
        }

        return $result;
    }
}