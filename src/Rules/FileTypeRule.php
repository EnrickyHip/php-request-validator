<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\FileInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Enums\FileType;

class FileTypeRule extends ValidationRule
{
    /** @param FileType[] $types */
    private array $types;
    protected string $message = "file :attributeName has an invalid type.";

    /** @param FileType[]|FileType $types */
    public function __construct(array|FileType $types, ?string $message = null)
    {
        parent::__construct($message);

        if (!is_array($types)) {
            $types = [$types];
        }

        $this->types = $types;
    }

    public function validate(mixed $value): bool
    {
        if (!$value instanceof FileInterface) {
            return false;
        }

        if (!$value->getType()) {
            return false;
        }

        return in_array($value->getType(), $this->types);
    }
}
