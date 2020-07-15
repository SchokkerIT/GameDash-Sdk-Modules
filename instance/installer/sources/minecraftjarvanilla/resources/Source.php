<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarVanilla\Resources;

    use function \_\map;
    use \Electrum\Uri\Uri;
    use \Electrum\Pagination\Pagination;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarVanilla\Lib\Api\Client\Client as ApiClient;
    use \GameDash\Sdk\FFI\Instance\Installer\Source\Resource\Result;

    class Source extends Implementation\Instance\Installer\Source\Source {

        /** @var Gateway\Gateway */
        private $Gateway;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

        }

        public function getResources( Pagination $Pagination ): Result\PaginatedResult {

            $Result = new Result\PaginatedResult( [ $this->getResource('vanilla') ] );

                $Result->setPagination( new Result\Pagination( $Pagination->getPage(), $Pagination->getPerPage() ) );

                $Result->setIsLast( true );

            return $Result;

        }

        public function count(): int {

            return 1;

        }

        public function getResource( string $id ): Implementation\Instance\Installer\Source\Resource\Resource {

            $Resource = new Resource\Resource( $id );

                $Resource->setTitle('Vanilla');
                $Resource->setDescription('The default Minecraft server.');
                $Resource->setVersions( $this->getVersions() );
                $Resource->setAuthor( new Resource\Author('Mojang') );

            return $Resource;

        }

        public function resourceExists(string $id ): bool {

            return true;

        }

        /** @return Resource\Version[] */
        private function getVersions(): array {

            $manifest = $this->getManifest();

            return map($manifest['versions'], static function( $result ) use ( $manifest ): Resource\Version {

                $Version = new Resource\Version( $result['id'] );

                    $Version->setIsLatest( $result['id'] === $manifest['latest']['release'] );

                return $Version;

            });

        }

        private function getManifest(): array {

            if( !$this->Gateway->getModule()->getDataCacheManager()->exists('manifest') ) {

                $Request = ApiClient::createRequest( HttpMethodsEnum::get(), Uri::fromString( 'https://launchermeta.mojang.com/mc/game/version_manifest.json' ) );

                $Request->send();

                $this->Gateway->getModule()->getDataCacheManager()->set('manifest', $Request->getResponse()->getAsJson());

            }

            return (array)$this->Gateway->getModule()->getDataCacheManager()->get('manifest');

        }

    }

?>
