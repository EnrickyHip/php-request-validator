<?php

namespace Enricky\RequestValidator\Traits;

use Closure;
use Enricky\RequestValidator\Rules\IsProhibitedRule;
use Enricky\RequestValidator\Rules\IsRequiredRule;


trait RequiredRulesSimplifier
{
    public function isRequired(?string $msg = null): self
    {
        $rule = new IsRequiredRule($msg);
        $this->addRule($rule);
        return $this;
    }

    /** @param bool|Closure(): bool $condition  */
    public function isRequiredIf(bool|Closure $condition, ?string $msg = null): self
    {
        $rule = new IsRequiredRule($msg, $condition);
        $this->addRule($rule);
        return $this;
    }

    /** @param bool|Closure(): bool $condition  */
    public function prohibitedIf(bool|Closure $condition, ?string $msg = null): self
    {
        $rule = new IsProhibitedRule($condition, $msg);
        $this->addRule($rule);
        return $this;
    }
}
