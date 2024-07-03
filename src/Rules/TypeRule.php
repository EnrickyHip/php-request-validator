<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Types\DataType;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;
use Enricky\RequestValidator\Exceptions\NoTypeSentException;

/** Rule to validate the data type of a field. */
class TypeRule extends ValidationRule
{
    /** @var DataTypeInterface|DataTypeInterface[] $types  */
    private DataTypeInterface|array $types;
    private bool $strict;
    protected string $message = "field :name is not of type: :type";

    /**
     * Create a new TypeRule instance.
     *
     * @param DataTypeInterface|string|(string|DataTypeInterface)[] $types The data types to validate against.
     * @param string|null $message The custom error message for the rule.
     * @param bool $strict set strict type validation
     */
    public function __construct(DataTypeInterface|array|string $types, ?string $message = null, bool $strict = true)
    {
        if ($types instanceof DataTypeInterface) {
            $this->types = $types;
        } elseif (is_string($types)) {
            $this->types = self::getDataTypeFromString($types);
        } else {
            if (empty($types)) {
                throw new NoTypeSentException("you sould send at least one data type.");
            }

            $this->types = array_map(function (DataTypeInterface|string $type) {
                if ($type instanceof DataTypeInterface) {
                    return $type;
                }

                return self::getDataTypeFromString($type);
            }, $types);
        }

        parent::__construct($message);
        $this->strict = $strict;
        $this->params = [
            ":type" => $this->getParam(),
        ];
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_array($this->types)) {
            foreach ($this->types as $type) {
                if ($this->validateType($type, $value)) {
                    return true;
                }
            }

            return false;
        }

        return $this->validateType($this->types, $value);
    }

    /** @return DataTypeInterface|DataTypeInterface[] */
    public function getType(): DataTypeInterface|array
    {
        return $this->types;
    }

    public function isMajor(): bool
    {
        return true;
    }

    public function getStrictMode(): bool
    {
        return $this->strict;
    }

    private function validateType(DataTypeInterface $type, mixed $value): bool
    {
        if ($this->strict) {
            return $type->strictValidate($value);
        }

        return $type->validate($value);
    }

    private function getParam(): string
    {
        if (is_array($this->types)) {
            $mappedTypes = array_map(
                fn (DataTypeInterface $type) => $type->getName(),
                $this->types
            );

            return join(" | ", $mappedTypes);
        }

        return $this->types->getName();
    }

    /**
     * @throws InvalidDataTypeException If the provided data type is not part of DataType enum.
     */
    public static function getDataTypeFromString(string $type): DataTypeInterface
    {
        $typeEnum = DataType::tryFrom(strtolower($type));

        if (!$typeEnum) {
            throw new InvalidDataTypeException("Value '$type' is not a valid data type.");
        }

        return $typeEnum;
    }
}
