<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Traits\TypeRuleSimplifier;

class FieldValidator extends Validator
{
    use TypeRuleSimplifier;
}
