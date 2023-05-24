<?php

namespace Enricky\RequestValidator\Traits;

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Rules\TypeRule;

trait TypeRuleSimplifier
{
    public function type(DataType|string $type, ?string $msg = null): self
    {
        $rule = new TypeRule($type, $msg);
        $this->addRule($rule);
        return $this;
    }
}
