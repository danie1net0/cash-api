<?php

namespace App\Exceptions\Services\WeatherService;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class UnauthenticatedException extends HttpException
{
    public function __construct($message = 'Não foi possível autenticar o serviço, verifique os parâmetros informados.', ?Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(401, $message, $previous, $headers, $code);
    }
}
