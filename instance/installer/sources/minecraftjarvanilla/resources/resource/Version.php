<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarVanilla\Resources\Resource;

    use function \_\find;
    use \Electrum\Uri\Uri;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarVanilla\Lib\Api\Client\Client as ApiClient;

    class Version extends Implementation\Instance\Installer\Source\Resource\Version {

        public function getUri(): Uri {

            $Request = ApiClient::createRequest(HttpMethodsEnum::get(), Uri::fromString( 'https://launchermeta.mojang.com/mc/game/version_manifest.json' ));

            $Request->send();

            $results = $Request->getResponse()->getAsJson();

            return Uri::fromString(

                find($results['versions'], function( $version ) {

                    return $version['id'] === $this->getId();

                })['url']

            );

        }

    }

?>
