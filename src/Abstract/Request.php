<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Abstract;

abstract class Request
{
  /** @return FieldValidator[] */
  abstract public function rules(array $data): array;

  /** @return string[] */
  final public function validate(array $data): array
  {
    $validators = $this->rules($data);
    $errors = [];

    foreach ($validators as $validator) {
      $errors = [...$errors, ...$validator->getErrors()];
    }

    return array_unique($errors);
  }
}
