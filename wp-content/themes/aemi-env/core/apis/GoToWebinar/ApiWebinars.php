<?php

class ApiWebinars
{
    /**
     * (Número de horas). Se as horas restantes de expiração da API forem menores que o número de horas definidas nesse atributo,o token será renovado.
     * @var int
     */
    private $time_expiration_token = 1;


    /**
     * Método Responsável por Iniciar a Classe Verificar os Tokens de Acesso
     * @return void
     */
    public function init(): void
    {
        if (!$this->getTokenDatabase()) {
            $this->requestTokenApi();

            return;
        }

        $this->refreshTokenApi();
    }


    /**
     * @param $method
     * @param $endpoint
     * @param $body
     * @return array|WP_Error
     */
    public function newRequest($method, $endpoint, $body)
    {
        $request = wp_remote_request(URL_GOTOWEBINAR.$endpoint, array(
            'method' => $method,
            'body' => $body,
            'headers' => [
                'Authorization' => 'Bearer '.$this->getTokenDatabase()->token,
                'Content-type' => 'application/json',
                'Accept' => 'application/json'
            ],
        ));

        return $request;
    }


    /**
     * @param $request
     * @return object|bool
     */
    public function getBodyResponse($request): ?object
    {
        $response = wp_remote_retrieve_body($request);
        $response = json_decode($response);

        return $response;
    }


    /**
     * Verifica se os dados do Token existem no banco, e se existirem retornam um Array com as informações
     * @return object|bool
     */
    public function getTokenDatabase()
    {
        $option = get_option('token_api_webinar');

        return $option ? (object) unserialize($option) : false;
    }


    /**
     * Atualiza o Token no banco de dados
     * @return void
     */
    private function updateTokenDatabase($token = null, $refresh_token = null, $account_key = null, $organizer_key = null): void
    {
        update_option('token_api_webinar', serialize([
            'token' => $token,
            'refresh_token' => $refresh_token,
            'account_key' => $account_key,
            'organizer_key' => $organizer_key,
            'time' => date('Y-m-d H:i:s', strtotime('now'))
        ]));
    }


    /**
     * Requisita um novo Token de Acesso para a API de autenticação, e atualiza os dados no banco
     * @return bool
     */
    private function requestTokenApi(): bool
    {
        $request = wp_remote_post('https://authentication.logmeininc.com/oauth/token', array(
            'headers' => array('Authorization' => 'Basic '.Base64Encode::getCode()),
            'body'    => array(
                'grant_type'    => 'authorization_code',
                'code'          => CODE_AUTHORIZATION,
                'redirect_uri'  => home_url('/'),
            ),
        ));

        if (is_wp_error($request)) {
            return false;
        }

        $response = wp_remote_retrieve_body($request);
        $response = (object) json_decode($response);

        if (isset($response->error)) {
            return false;
        }

        $this->updateTokenDatabase($response->access_token, $response->refresh_token);
        $this->refreshTokenApi();

        return true;
    }


    /**
     * Método responsável por renovar o Token
     * @return bool
     */
    private function refreshTokenApi(): bool
    {
        if (!$this->getTokenDatabase()) {
            return false;
        }

        $request = wp_remote_post('https://api.getgo.com/oauth/v2/token', array(
            'headers' => array('Authorization' => 'Basic '.Base64Encode::getCode()),
            'body'    => array(
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->getTokenDatabase()->refresh_token,
            ),
        ));

        if (is_wp_error($request)) {
            return false;
        }

        $response = wp_remote_retrieve_body($request);
        $response = json_decode($response);

        $this->updateTokenDatabase(
            $response->access_token,
            $response->refresh_token,
            $response->account_key,
            $response->organizer_key,
        );

        return true;
    }


    /**
     * Verifica se o Token criado tem menos de 1 dia de validade
     * @return int|bool
     * @throws Exception
     */
    private function validateTimeTokenApi(): ?int
    {
        if (!$this->getTokenDatabase()) {
            return false;
        }

        $token              = Jwt::decode($this->getTokenDatabase()->token);
        $date_now           = date('Y-m-d H:i:s', strtotime('now'));
        $expiration_token   = date('Y-m-d H:i:s', $token->exp);

        $time = new DateTime($expiration_token);
        $diff = $time->diff(new DateTime($date_now));

        return $diff->i < $this->time_expiration_token;
    }


    /**
     * Verifica se os requisitos para a criação de um novo Token são atendidos
     * @return bool
     */
    private function checkValidateApiToken(): bool
    {
        if (!$this->validateTimeTokenApi()) {
            return false;
        }

        $this->refreshTokenApi();

        return true;
    }
}