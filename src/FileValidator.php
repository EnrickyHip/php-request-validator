<?php

declare(strict_types=1);

namespace Enricky\RequestValidator;

use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Rules\IsFileRule;
use Enricky\RequestValidator\Traits\FilesRulesSimplifier;

class FileValidator extends Validator
{
    use FilesRulesSimplifier;

    public function __construct(AttributeInterface $attribute, ?string $message = null)
    {
        parent::__construct($attribute);
        $this->rules[] = new IsFileRule($message);
    }
}
