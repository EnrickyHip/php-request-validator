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
    private bool $strict;
    protected string $message = "field :name is not of type :type";

    /**
     * Create a new TypeRule instance.
     *
     * @param DataType|string $type The data type to validate against.
     * @param string|null $message The custom error message for the rule.
     * @param bool $strict set strict type validation
     *
     * @throws InvalidDataTypeException If the provided data type is not part of DataType enum.
     */
    public function __construct(DataType|string $type, ?string $message = null, bool $strict = true)
    {
        if (is_string($type)) {
            $type = DataType::tryFrom(strtolower($type));
        }

        if (!$type) {
            throw new InvalidDataTypeException("Value '$type' is not a valid data type.");
        }

        parent::__construct($message);
        $this->type = $type;
        $this->strict = $strict;
        $this->params = [
            ":type" => $this->type->value,
        ];
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if ($this->strict) {
            return $this->type->strictValidate($value);
        }

        return $this->type->validate($value);
    }

    public function getType(): DataType
    {
        return $this->type;
    }

    public function isMajor(): bool
    {
        return true;
    }

    public function getStrictMode(): bool
    {
        return $this->strict;
    }
}
