<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftBedrock\Resources;

    use function \_\last;
    use \Electrum\Pagination\Pagination;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance\Installer\Source\Resource\Result;

    class Source extends Implementation\Instance\Installer\Source\Source {

        public function getResources( Pagination $Pagination ): Result\PaginatedResult {

            $Result = new Result\PaginatedResult( [ $this->getResource('default') ] );

                $Result->setPagination(

                    new Result\Pagination(

                        $Pagination->getPage(),
                        $Pagination->getPerPage(),
                        $Pagination->getPage() * $Pagination->getPerPage() >= $Result->count()

                    )

                );

            return $Result;

        }

        public function getResource( string $id ): Implementation\Instance\Installer\Source\Resource\Resource {

            $Resource = new Resource\Resource( $id );

                $Resource->setTitle('Minecraft Bedrock');
                $Resource->setVersions( $this->getVersions() );
                $Resource->setAuthor( new Resource\Author('Microsoft') );

            return $Resource;

        }

        public function resourceExists(string $id ): bool {

            return $id === 'default';

        }

        /** @return Resource\Version[] */
        private function getVersions(): array {

            return [

                $this->getLatestVersion()

            ];

        }

        private function getLatestVersion(): Resource\Version {

            $Uri = Resource\Versions::getLatestDownloadUri();

            $fileNameBits = explode('.', last(explode('/', $Uri->toString())));

            array_pop($fileNameBits);

            $fileName = implode('.', $fileNameBits);

            $versionId = last(explode('-', $fileName));

            $Version = new Resource\Version( $versionId );

                $Version->setIsLatest( true );

            return $Version;

        }

    }

?>
