<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftBedrock\Resources\Resource;

    use \Electrum\Uri\Uri;
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

                new Path\Path( $Instance, '/bedrock.zip' )

            );

            $ZipFile->downloadFrom( $this->getDownloadUri( $Version ) );

            $ZipFile->unzip(

                $ZipFile->getParent()

            );

            $ZipFile->delete();

            $Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $Instance, 'bedrock_server' )

            )->setExecutable();

            return $Diff->diff();

        }

        public function uninstall( Instance\Instance $Instance, Record\Record $Record ): void {}

        public function beforeUpgrade( Instance\Instance $Instance ): void {

            $this->moveFileIfExists($Instance, 'server.properties', 'server.properties.tmp');
            $this->moveFileIfExists($Instance, 'permissions.json', 'permissions.json.tmp');
            $this->moveFileIfExists($Instance, 'whitelist.json', 'whitelist.json.tmp');

        }

        public function afterUpgrade( Instance\Instance $Instance ): void {

            $this->moveFileIfExists($Instance, 'server.properties.tmp', 'server.properties');
            $this->moveFileIfExists($Instance, 'permissions.json.tmp', 'permissions.json');
            $this->moveFileIfExists($Instance, 'whitelist.json.tmp', 'whitelist.json');

        }

        private function moveFileIfExists( Instance\INstance $Instance, string $path, string $destinationPath ): void {

            $File = $Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $Instance, $path )

            );

            if( !$File->exists() ) {

                return;

            }

            $File->move(

                $Instance->getFileSystem()->getFiles()->get(

                    new Path\Path( $Instance, $destinationPath )

                )

            );

        }

        private function getDownloadUri( Version $Version ): Uri {

            return Uri::fromString(

                'https://minecraft.azureedge.net/bin-linux/bedrock-server-' . $Version->getId() . '.zip'

            );

        }

    }

?>
