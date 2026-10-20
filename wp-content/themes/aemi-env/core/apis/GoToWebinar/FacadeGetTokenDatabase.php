<?php


class FacadeGetTokenDatabase
{
    /**
     * Retorna os dados dos Tokens da API salvos no Banco de Dados
     * @return object|bool
     */
    public static function get()
    {
        $api = new ApiWebinars();

        return $api->getTokenDatabase();
    }
}