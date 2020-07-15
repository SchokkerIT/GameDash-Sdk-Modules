<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\TShock\Resources;

    use function \_\map;
    use function \_\filter;
    use \Electrum\Pagination\Pagination;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\TShock\Lib\Api\Client\Client as ApiClient;
    use \GameDash\Sdk\FFI\Instance\Installer\Source\Resource\Result;

    class Source extends Implementation\Instance\Installer\Source\Source {

        public function getResources( Pagination $Pagination ): Result\PaginatedResult {

            return new Result\PaginatedResult( [ $this->getResource('default') ] );

        }

        public function count(): int {

            return 0;

        }

        public function getResource( string $id ): Implementation\Instance\Installer\Source\Resource\Resource {

            $Resource = new Resource\Resource( $id );

                $Resource->setTitle('TShock');
                $Resource->setVersions( $this->getVersions() );
                $Resource->setAuthor( new Resource\Author('Pryaxis') );

            return $Resource;

        }

        public function resourceExists( string $id ): bool {

            return $id === 'default';

        }

        /** @return Resource\Version[] */
        private function getVersions(): array {

            $Request = ApiClient::createRequest( HttpMethodsEnum::get(), 'repos/Pryaxis/TShock/releases' );

            $Request->send();

            return filter(

                map($Request->getResponse()->getAsJson(), static function( array $release, int $index ): ?Resource\Version {

                    if( $release['prerelease'] === true ) {

                        return null;

                    }

                    $Version = new Resource\Version( $release['id'] );

                        $Version->setName( $release['name'] );
                        $Version->setIsLatest( $index === 0 );

                    return $Version;

                }),
                static function( ?Resource\Version $Version ) {

                    return $Version !== null;

                }

            );

        }

    }

?>
