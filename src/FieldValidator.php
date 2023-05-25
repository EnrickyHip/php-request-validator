<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Rules\TypeRule;

class FieldValidator extends Validator
{
    public function type(DataType|string $type, ?string $msg = null): self
    {
        $rule = new TypeRule($type, $msg);
        $this->addRule($rule);
        return $this;
    }
}
