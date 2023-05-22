<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

abstract class Request
{
    /** @return ValidatorInterface[] */
    abstract public function rules(array $data): array;

    final public function validate(array $data): bool
    {
        return empty($this->getErrors($data));
    }

    /** @return string[] */
    final public function getErrors(array $data): array
    {
        $validators = $this->rules($data);
        $errors = [];

        foreach ($validators as $validator) {
            $errors = [...$errors, ...$validator->getErrors()];
        }

        return array_unique($errors);
    }
}
