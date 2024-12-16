<?php

namespace App\Exceptions\Services\WeatherService;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CityNotFoundException extends HttpException
{
    public function __construct($message = 'Cidade não encontrada, verifique o valor informado.', ?Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(404, $message, $previous, $headers, $code);
    }
}
