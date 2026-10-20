<?php


class Base64Encode
{
    /**
     * @return string
     */
    public static function getCode(): string
    {
        return self::generateCodeAuthorization();
    }

    /**
     * @param $params
     * @return string
     */
    public static function hash($params): string
    {
        return base64_encode($params);
    }

    /**
     * @return string
     */
    private static function generateCodeAuthorization(): string
    {
        return base64_encode(CLIENT_ID.':'.CLIENT_SECRET);
    }
}