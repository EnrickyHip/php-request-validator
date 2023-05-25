<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\FileInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;

class IsFileRule extends ValidationRule
{
    protected string $message = "field :attributeName is not a valid file";

    public function validate(mixed $value): bool
    {
        if (!$value instanceof FileInterface) {
            return false;
        }

        if ($value->getName() === "") {
            return false;
        }

        if ($value->getFullPath() === "") {
            return false;
        }

        if (!$value->getType()) {
            return false;
        }

        if (!file_exists($value->getTempName())) {
            return false;
        }

        if ($value->getError() !== UPLOAD_ERR_OK) {
            return false;
        }

        return true;
    }

    public function isMajor(): bool
    {
        return true;
    }
}
