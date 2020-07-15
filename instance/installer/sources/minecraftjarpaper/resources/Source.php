<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarPaper\Resources;

    use function \_\map;
    use \Electrum\Pagination\Pagination;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Paper;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarPaper\Lib\Api\Client\Query\QueryManager as ApiClientQueryManager;
    use \GameDash\Sdk\FFI\Instance\Installer\Source\Resource\Result;

    class Source extends Implementation\Instance\Installer\Source\Source {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var ApiClientQueryManager */
        private $ApiClientQueryManager;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;
            $this->ApiClientQueryManager = new ApiClientQueryManager( $Gateway );

        }

        public function getResources( Pagination $Pagination ): Result\PaginatedResult {

            $Result = new Result\PaginatedResult( [ $this->getResource('vanilla') ] );

                $Result->setPagination( new Result\Pagination( $Pagination->getPage(), $Pagination->getPerPage() ) );

                $Result->setIsLast( true );

            return $Result;

        }

        public function getResource( string $id ): Implementation\Instance\Installer\Source\Resource\Resource {

            $Resource = new Resource\Resource( $this->Gateway, $id );

                $Resource->setTitle('Paper');
                $Resource->setDescription('Performant Minecraft Spigot/CraftBukkit fork');
                $Resource->setVersions( $this->getVersions() );
                $Resource->setAuthor( new Resource\Author('PaperMC') );

            return $Resource;

        }

        public function resourceExists( string $id ): bool {

            return true;

        }

        /**
         * @return Resource\Version[]
         * @throws \Electrum\Json\InvalidJsonException
         * @throws \Electrum\Userland\Sdk\Module\Cache\Data\CacheManagerException
         */
        private function getVersions(): array {

            return map($this->ApiClientQueryManager->getVersionIds(), static function( string $id, int $index ): Resource\Version {

                $Version = new Resource\Version( $id );

                    $Version->setIsLatest( $index === 0 );

                return $Version;

            });

        }

    }

?>
