<?php

declare(strict_types=1);

use Enricky\RequestValidator\Abstract\ValidationRule;

class ValidationRuleWithParams extends ValidationRule
{
    private string $param;
    private mixed $otherParam;
    protected string $message = "the field :name with value :value is not valid with :param and :otherParam";

    public function __construct(string $param, mixed $otherParam)
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

it("should replace custom parameters", function (string $param, mixed $otherParam, string $otherParamRepresentation) {
    $field = new AttributeMock("testName", "testValue");
    $testRule = new ValidationRuleWithParams($param, $otherParam);
    expect($testRule->resolveMessage($field))->toBe("the field 'testName' with value 'testValue' is not valid with '$param' and $otherParamRepresentation");
})->with([
    ["test", "otherTest", "'otherTest'"],
    ["test2", 1, "1"],
    ["test3", 10.5, "10.5"],
    ["test4", true, "true"],
    ["test5", false, "false"],
    ["test6", [], "[array]"],
    ["test7", new stdClass(), "{object}"],
    ["test8", null, "null"],
]);
