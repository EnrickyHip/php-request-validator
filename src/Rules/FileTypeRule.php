<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\FileInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Enums\FileType;

class FileTypeRule extends ValidationRule
{
    /** @var FileType[] $types */
    private array $types;

    protected string $message = "file :attributeName has an invalid type.";

    /** @param (FileType|string)[]|FileType|string $types */
    public function __construct(array|string|FileType $types, ?string $message = null)
    {
        parent::__construct($message);

        if (!is_array($types)) {
            $types = [$types];
        }

        $this->types = array_map(function (string|FileType $type) {
            if (is_string($type)) {
                return FileType::getFromExtension($type);
            }

            return $type;
        }, $types);
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

    /** @return FileType[] */
    public function getTypes(): array
    {
        return $this->types;
    }
}
