<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use BackedEnum;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Exceptions\InvalidEnumException;

/** Rule to validate if a value is part of a given enum class. */
class ValidEnumRule extends ValidationRule
{
    private string $enumClass;
    protected string $message = "field :fieldName is not a part of the enum :enum";

    /**
     * Create a new ValidEnumRule instance.
     *
     * @param string $enumClass The class name of the enum.
     * @param string|null $message The custom error message for the rule.
     *
     * @throws InvalidEnumException If the provided string is not a backed enum class.
     *
     * ```
     * $rule = new ValidEnumRule(DataType::class);
     * $rule->validate("random text"); //false
     * $rule->validate("int"); //true
     * $rule->validate("string"); //true
     * ```
     */
    public function __construct(string $enumClass, ?string $message = null)
    {
        if (!is_subclass_of($enumClass, BackedEnum::class)) {
            throw new InvalidEnumException("class is not a Backed Enum!");
        }

        parent::__construct($message);
        $this->enumClass = $enumClass;
        $this->params = [
            ":enum" => $enumClass
        ];
    }

    public function validate(mixed $value): bool
    {
        if ($this->getBackingType() !== gettype($value)) {
            return false;
        }

        return (bool)$this->enumClass::tryFrom($value);
    }

    private function getBackingType(): string
    {
        return gettype($this->enumClass::cases()[0]->value);
    }
}
