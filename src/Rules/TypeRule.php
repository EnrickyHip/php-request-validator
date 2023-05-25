<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;


/** Rule to validate the data type of a field. */
class TypeRule extends ValidationRule
{
    private DataType $type;
    protected string $message = "field :attributeName is not of type :type";

    /**
     * Create a new TypeRule instance.
     *
     * @param DataType|string $type The data type to validate against.
     * @param string|null $message The custom error message for the rule.
     *
     * @throws InvalidDataTypeException If the provided data type is not part of DataType enum.
     */
    public function __construct(DataType|string $type, ?string $message = null)
    {
        if (is_string($type)) {
            $type = DataType::tryFrom(strtolower($type));
        }

        if (!$type) {
            throw new InvalidDataTypeException("Value '$type' is not a valid data type.");
        }

        parent::__construct($message);
        $this->type = $type;
        $this->params = [
            ":type" => $this->type->value,
        ];
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        return $this->type->validate($value);
    }

    public function isMajor(): bool
    {
        return true;
    }
}
