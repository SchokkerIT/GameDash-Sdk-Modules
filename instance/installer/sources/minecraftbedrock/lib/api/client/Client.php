<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftBedrock\Lib\Api\Client;

    use \Electrum\Http;
    use \Electrum\Uri\Uri;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;

    class Client {

        public static function createRequest( HttpMethodsEnum $Method, Uri $Uri ): Http\Client\Request {

            return Http\Client\Client::createRequest($Method, $Uri);

        }

    }

?>
