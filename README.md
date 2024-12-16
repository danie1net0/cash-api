# Cash Api

## Chaves de API

### Open Weather
1. Acesse a [página de cadastro](https://home.openweathermap.org/users/sign_up) da OpenWeather.
2. Insira seu nome, um e-mail e senha e confirme o e-mail através da notificação que será enviada.
3. Após confirmar o cadastro, vá para a página [API keys](https://home.openweathermap.org/api_keys) no dashboard da OpenWeather.
4. Adicione um nome para a chave no campo 'Create key' e clique no botão 'Generate'.
5. Copie a chave gerada e cole no valor da variável `OPENWEATHER_API_KEY=` do arquivo `.env` da aplicação.

### Weather API
1. Acesse a [página de cadastro](https://www.weatherapi.com/signup.aspx) da Weather API.
2. Insira um e-mail e senha e confirme o e-mail através da notificação que será enviada.
3. Realize a autenticação com a conta criada.
4. Após a autenticação, haverá um redirecionamento para o dashboard e a chave de API já estará disponível.
5. Copie a chave e cole no valor da variável `WEATHERAPI_API_KEY=` do arquivo `.env` da aplicação.

## Ambiente de desenvolvimento

1. Clonar o repostitório:
    ```bash
   git clone git@github.com:danie1net0/cash-api.git && cd cash-api
   ```
   
2. Criar o arquivo `.env`:
    ```bash
   cp .env.example .env
   ```
   > Alterar a porta da aplicação `APP_PORT`, se necessário

   > Adiciona as chaves das APIs de tempo `OPENWEATHER_API_KEY` e/ou `WEATHERAPI_API_KEY`
   
3. Instalar dependências do Composer:
   ```bash
   docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
   ```
    > A aplicação estará disponível em `http://localhost:8080`, ou na porta definida anteriormente

4. Cria o contêiner:
   ```bash
   ./vendor/bin/sail up -d
   ```

5. Criar chave da aplicação:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```
