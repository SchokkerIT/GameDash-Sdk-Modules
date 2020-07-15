<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarPaper\Lib\Api\Client;

    use \Electrum\Http;
    use \Electrum\Uri\Uri;
    use \Electrum\Enums\Network\Http\Protocols as HttpProtocolsEnum;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;

    class Client {

        public static function createRequest( HttpMethodsEnum $Method, string $endpoint ): Http\Client\Request {

            $Request = Http\Client\Client::createRequest($Method, Uri::fromString( self::getBaseUri()->toString() . '/' . $endpoint ));

            $Request->getHeaders()->get('User-Agent')->setValue('GameDash');

            return $Request;

        }

        public static function getBaseUri(): Uri {

            return Uri::build(

                HttpProtocolsEnum::https(),
                'papermc.io',
                'api/v1'

            );

        }

    }

?>
