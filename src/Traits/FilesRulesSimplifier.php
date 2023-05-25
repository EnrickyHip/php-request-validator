<?php

namespace Enricky\RequestValidator\Traits;

use Enricky\RequestValidator\Enums\FileType;
use Enricky\RequestValidator\Rules\FileTypeRule;
use Enricky\RequestValidator\Rules\MaxFileSizeRule;

trait FilesRulesSimplifier
{
    /** @param FileType[]|FileType $types */
    public function type(array|FileType $types, ?string $message = null): self
    {
        $rule = new FileTypeRule($types, $message);
        $this->addRule($rule);
        return $this;
    }

    public function maxSize(int $size, ?string $message = null): self
    {
        $rule = new MaxFileSizeRule($size, $message);
        $this->addRule($rule);
        return $this;
    }
}
