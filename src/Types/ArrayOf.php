<?php

namespace Enricky\RequestValidator\Types;

use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Exceptions\NoTypeSentException;
use Enricky\RequestValidator\Rules\TypeRule;

class ArrayOf implements DataTypeInterface
{
    /**
     *  @var DataTypeInterface|DataTypeInterface[]|null $types
     */
    private DataTypeInterface|array|null $types = null;

    /**
     * @param DataTypeInterface|string|(string|DataTypeInterface)[] $types The data types to validate against.
     */
    public function __construct(DataTypeInterface|string|array|null $types = null)
    {
        if ($types instanceof DataTypeInterface || is_null($types)) {
            $this->types = $types;
        } elseif (is_string($types)) {
            $this->types = TypeRule::getDataTypeFromString($types);
        } else {
            if (empty($types)) {
                throw new NoTypeSentException("you sould send at least one data type.");
            }

            $this->types = array_map(function (DataTypeInterface|string $type) {
                if ($type instanceof DataTypeInterface) {
                    return $type;
                }

                return TypeRule::getDataTypeFromString($type);
            }, $types);
        }
    }

    public function strictValidate(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$this->types) {
            return true;
        }

        if (is_array($this->types)) {
            foreach ($value as $element) {
                $elementIsValid = $this->strictValidateAgainstTypes($element);

                if (!$elementIsValid) {
                    return false;
                }
            }

            return true;
        }

        foreach ($value as $element) {
            if (!$this->types->strictValidate($element)) {
                return false;
            }
        }

        return true;
    }

    public function validate(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$this->types) {
            return true;
        }

        if (is_array($this->types)) {
            foreach ($value as $element) {
                $elementIsValid = $this->validateAgainstTypes($element);

                if (!$elementIsValid) {
                    return false;
                }
            }

            return true;
        }

        foreach ($value as $element) {
            if (!$this->types->validate($element)) {
                return false;
            }
        }

        return true;
    }

    public function getName(): string
    {
        if (!$this->types) {
            return "array";
        }

        if (is_array($this->types)) {
            $mappedTypes = array_map(fn (DataTypeInterface $type) => $type->getName(), $this->types);
            $join = join("|", $mappedTypes);
            return "($join)[]";
        }

        return $this->types->getName() . "[]";
    }

    public function getType(): DataTypeInterface|array
    {
        return $this->types;
    }

    //TODO talvez unir esses métodos de validate?
    private function strictValidateAgainstTypes(mixed $element)
    {
        $elementIsValid = false;

        foreach ($this->types as $type) {
            if ($type->strictValidate($element)) {
                $elementIsValid = true;
            }
        }

        return $elementIsValid;
    }

    private function validateAgainstTypes(mixed $element)
    {
        $elementIsValid = false;

        foreach ($this->types as $type) {
            if ($type->validate($element)) {
                $elementIsValid = true;
            }
        }

        return $elementIsValid;
    }
}
