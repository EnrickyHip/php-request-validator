<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\FileInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;

class MaxFileSizeRule extends ValidationRule
{
    private int $size;
    protected string $message = "file :attributeName size is bigger than maximum.";

    public function __construct(int $size, ?string $message = null)
    {
        parent::__construct($message);
        $this->size = $size;
    }

    public function validate(mixed $value): bool
    {
        if (!$value instanceof FileInterface) {
            return false;
        }

        return $value->getSize() <= $this->size;
    }
}
