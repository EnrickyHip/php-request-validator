<?php

declare(strict_types=1);

namespace Enricky\RequestValidator\Rules;

use Enricky\RequestValidator\Abstract\ValidationRule;

/** Rule to validate if a value is a valid URL. */
class IsUrlRule extends ValidationRule
{
    private const URL_MATCH = "/^(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.
                        [^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})$/";

    protected string $message = "field :name is not a valid url";

    public function validate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return (bool)preg_match(self::URL_MATCH, $value);
    }
}
