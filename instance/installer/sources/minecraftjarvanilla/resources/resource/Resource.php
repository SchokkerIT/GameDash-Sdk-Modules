<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarVanilla\Resources\Resource;

    use \Electrum\Uri\Uri;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Installer\Record;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarVanilla\Lib\Api\Client\Client as ApiClient;

    class Resource extends Implementation\Instance\Installer\Source\Resource\Resource {

        public function install( Instance\Instance $Instance, array $options = [] ): array {

            $versionId = $options['versionId'] ?? null;

            $Version = $this->getVersion( $versionId );

            $Destination = $Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $Instance, '/vanilla_' . $Version->getId() . '.jar' )

            );

            $Destination->downloadFrom( $this->getDownloadUri( $Version ) );

            return [

                $Destination

            ];

        }

        public function uninstall( Instance\Instance $Instance, Record\Record $Record ): void {}

        private function getDownloadUri( Version $Version ): Uri {

            $Request = ApiClient::createRequest( HttpMethodsEnum::get(), $Version->getUri() );

            $Request->send();

            return Uri::fromString(

                $Request->getResponse()->getAsJson()['downloads']['server']['url']

            );

        }

    }

?>
