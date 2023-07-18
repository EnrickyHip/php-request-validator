# PHP REQUEST VALIDATOR

PHP Standalone Validation Library for HTTP Requests.

## Instalação

```shell
$ composer require enricky/cnpj-manager
```

## Utilização
```php
use Enricky\CnpjManager\Cnpj;
```


## Gere um CNPJ Válido aleatório

```php
$cnpj = Cnpj::generate();
echo $cnpj; // 35.796.587/0001-71
```

## Valide um CNPJ

```php
$cnpjValido = Cnpj::validate('56.616.297/0001-79');
var_dump($cnpjValido); //bool(true)

$cnpjInvalido = Cnpj::validate('22.222.222/2222-22');
var_dump($cnpjInvalido); //bool(false)
```

## Formate um CNPJ

```php
$cnpjFormatado = Cnpj::format('56616297000179');
echo $cnpjFormatado; // 56.616.297/0001-79
```

## Valide o formato de um CNPJ

```php
$formatoValido = Cnpj::validateFormat('22.222.222/2222-22');
var_dump($formatoValido); //bool(true)

$formatoInvalido = Cnpj::validateFormat('22/222/222/2222/22');
var_dump($formatoInvalido); //bool(false)
```

## Limpe um CNPJ

```php
$cnpjLimpo = Cnpj::cleanUp('56.616.297/0001-79');
echo $cnpjLimpo; // 56616297000179
```