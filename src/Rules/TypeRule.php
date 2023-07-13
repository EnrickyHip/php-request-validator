<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Types\DataType;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;


/** Rule to validate the data type of a field. */
class TypeRule extends ValidationRule
{
    private DataTypeInterface $type;
    private bool $strict;
    protected string $message = "field :name is not of type :type";

    /**
     * Create a new TypeRule instance.
     *
     * @param DataTypeInterface|string $type The data type to validate against.
     * @param string|null $message The custom error message for the rule.
     * @param bool $strict set strict type validation
     *
     * @throws InvalidDataTypeException If the provided data type is not part of DataType enum.
     */
    public function __construct(DataTypeInterface|string $type, ?string $message = null, bool $strict = true)
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
            ":type" => $this->type->getName(),
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

    public function getType(): DataTypeInterface
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
