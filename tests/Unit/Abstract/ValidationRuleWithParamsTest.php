<?php

declare(strict_types=1);

use Enricky\RequestValidator\Abstract\ValidationRule;

class ValidationRuleWithParams extends ValidationRule
{
    private string $param;
    private string $otherParam;
    protected string $message = "the field :fieldName with value :fieldValue is not equal to :param or :otherParam";

    public function __construct(string $param, string $otherParam)
    {
        $this->param = $param;
        $this->otherParam = $otherParam;
        $this->params = [
            ":param" => $this->param,
            ":otherParam" => $this->otherParam,
        ];
    }

    public function validate(mixed $value): bool
    {
        return $value === "valid value";
    }
}

it("should replace custom parameters", function (string $param, string $otherParam) {
    $field = new FieldMock("testName", "testValue");
    $testRule = new ValidationRuleWithParams($param, $otherParam);
    expect($testRule->resolveMessage($field))->toBe("the field testName with value testValue is not equal to $param or $otherParam");
})->with([
    ["test", "otherTest"],
    ["Enricky", "otherEnricky"],
    ["randomTest", "otherRandomTest"],
    ["", "other"]
]);
