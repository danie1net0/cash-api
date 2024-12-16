<?php

namespace App\Exceptions\Services\WeatherService;

use Exception;

class NoServiceAvailable extends Exception
{
    public function __construct(
        string $message = 'Não foi possível registrar nenhum serviço, verifique os parâmetros informados.'
    ) {
        parent::__construct($message);
    }
}
