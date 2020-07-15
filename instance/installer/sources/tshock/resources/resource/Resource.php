<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\TShock\Resources\Resource;

    use function \_\find;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \Electrum\Uri\Uri;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\TShock\Lib\Api\Client\Client as ApiClient;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Installer\Record;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class Resource extends Implementation\Instance\Installer\Source\Resource\Resource {

        public function install( Instance\Instance $Instance, array $options = [] ): array {

            $versionId = $options['versionId'] ?? null;

            $Diff = $Instance->getFileSystem()->getFiles()->getDiffer()->diffDirectory(

                $Instance->getFileSystem()->getRootDirectory()->getFile()

            );

            $Version = $this->getVersion( $versionId );

            $ZipFile = $Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $Instance, '/tshock.zip' )

            );

            $ZipFile->downloadFrom( $this->getDownloadUri( $Version ) );

            $ZipFile->unzip(

                $ZipFile->getParent()

            );

            $ZipFile->delete();

            $Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $Instance, 'TerrariaServer.exe' )

            )->setExecutable();

            return $Diff->diff();

        }

        public function uninstall( Instance\Instance $Instance, Record\Record $Record ): void {}

        public function beforeUpgrade( Instance\Instance $Instance ): void {}

        public function afterUpgrade( Instance\Instance $Instance ): void {}

        private function getDownloadUri( Version $Version ): Uri {

            $Request = ApiClient::createRequest( HttpMethodsEnum::get(), 'repos/Pryaxis/TShock/releases/' . $Version->getId() );

            $Request->send();

            $version = $Request->getResponse()->getAsJson();

            return Uri::fromString( $version['assets'][0]['browser_download_url'] );

        }

    }

?>
