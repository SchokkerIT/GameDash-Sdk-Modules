<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Spiget\Resources;

    use function \_\map;
    use function \_\filter;
    use \Electrum\Time\Time;
    use \Electrum\Uri\Uri;
    use \Electrum\Base64\Base64;
    use \Electrum\Pagination\Pagination;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Spiget;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Spiget\Lib\Api\Client\Client as ApiClient;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Spiget\Lib\Api\Client\Query\QueryManager as ApiClientQueryManager;
    use \GameDash\Sdk\FFI\Instance\Installer\Source\Resource\Result;

    class Source
        extends Implementation\Instance\Installer\Source\Source
        implements
            Implementation\Instance\Installer\Source\IWithResourceSearching,
            Implementation\Instance\Installer\Source\IWithResourceIcons,
            Implementation\Instance\Installer\Source\IWithCategories
    {

        use Implementation\Instance\Installer\Source\UseCategoriesTrait;

        /** @var ApiClientQueryManager */
        private $ApiClientQueryManager;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->ApiClientQueryManager = new ApiClientQueryManager( $Gateway );

        }

        public function searchResources( string $query, Pagination $Pagination ): Result\PaginatedResult {

            $Request = ApiClient::createRequest( HttpMethodsEnum::get(), 'search/resources/' . $query );

            //add 1 to page because of bug with spiget
            $Request->getParameters()->get('page')->setValue( $Pagination->getPage() + 1 );
            $Request->getParameters()->get('size')->setValue( $Pagination->getPerPage() );
            $Request->getParameters()->get('fields')->setValue('id,name,file,tag,downloads,icon,releaseDate');
            $Request->getParameters()->get('sort')->setValue('-updateDate');

            $Request->send();

            $resources = map(

                filter($Request->getResponse()->getAsJson(), static function( $result) { return $result['file']['type'] !== 'external'; }),

                static function($result ): Resource\Resource {

                    $Resource = new Resource\Resource( $result['id'] );

                        $Resource->setTitle( $result['name'] );
                        $Resource->setDescription( $result['tag'] );

                        if( !empty( $result['icon']['data'] ) ) {

                            $Resource->setIconUri( Uri::fromString( 'data:image/jpeg;charset=utf-8;base64,' . $result['icon']['data'] ) );

                        }

                        $Resource->setDownloadCount( $result['downloads'] );
                        $Resource->setTimeCreated( Time::createFromTimestamp( $result['releaseDate'] ) );

                    return $Resource;

                }

            );

            $Result = new Result\PaginatedResult( $resources );

                $Result->setPagination( new Result\Pagination( $Pagination->getPage(), $Pagination->getPerPage(), false ) );

            return $Result;

        }

        public function getResources( Pagination $Pagination ): Result\PaginatedResult {

            if( $this->getUsedCategory() ) {

                $Request = ApiClient::createRequest( HttpMethodsEnum::get(), 'categories/' . $this->getUsedCategory()->getId() . '/resources' );

                //add 1 to page because of bug with spiget
                $Request->getParameters()->get('page')->setValue( $Pagination->getPage() + 1 );
                $Request->getParameters()->get('size')->setValue( $Pagination->getPerPage() );
                $Request->getParameters()->get('fields')->setValue('id,name,description,tag,downloads,icon,file,releaseDate');
                $Request->getParameters()->get('sort')->setValue('-updateDate');

                $Request->send();

            }
            else {

                $Request = ApiClient::createRequest( HttpMethodsEnum::get(), 'resources/new' );

                $Request->getParameters()->get('page')->setValue( $Pagination->getPage() + 1 );
                $Request->getParameters()->get('size')->setValue( $Pagination->getPerPage() );
                $Request->getParameters()->get('fields')->setValue('id,name,description,tag,downloads,icon,file,releaseDate');
                $Request->getParameters()->get('sort')->setValue('-updateDate');

                $Request->send();

            }

            $resources = map(

                filter($Request->getResponse()->getAsJson(), static function( $result) { return $result['file']['type'] !== 'external'; }),

                static function($result ): Resource\Resource {

                    $Resource = new Resource\Resource( $result['id'] );

                        $Resource->setTitle( $result['name'] );
                        $Resource->setDescription( $result['tag'] );

                        if( !empty( $result['icon']['data'] ) ) {

                            $Resource->setIconUri( Uri::fromString( 'data:image/jpeg;charset=utf-8;base64,' . $result['icon']['data'] ) );

                        }

                        $Resource->setDownloadCount( $result['downloads'] );
                        $Resource->setTimeCreated( Time::createFromTimestamp( $result['releaseDate'] ) );

                    return $Resource;

                }

            );

            $Result = new Result\PaginatedResult( $resources );

                $Result->setPagination( new Result\Pagination( $Pagination->getPage(), $Pagination->getPerPage(), false ) );

            return $Result;

        }

        public function getResource( string $id ): Implementation\Instance\Installer\Source\Resource\Resource {

            $Resource = new Spiget\Resources\Resource\Resource( $id );

                $result = $this->ApiClientQueryManager->getResource( $id );

                $Resource->setTitle( $result['name'] );
                $Resource->setDescription( $result['tag'] );
                $Resource->setFullDescription( Base64::decode( $result['description'] ) );

                if( !empty( $result['icon']['data'] ) ) {

                    $Resource->setIconUri( Uri::fromString( 'data:image/jpeg;charset=utf-8;base64,' . $result['icon']['data'] ) );

                }

                $Resource->setVersions(map($result['versions'], static function( $version ) use ( $result ) {

                    $Version = new Resource\Version( $version['id'] );

                        $Version->setIsLatest( $result['version']['id'] === $version['id'] );

                    return $Version;

                }));
                $Resource->setTimeCreated( Time::createFromTimestamp( $result['releaseDate'] ) );

                $Resource->setAuthor(

                    $this->getAuthor( $result['author']['id'] )

                );

            return $Resource;

        }

        public function resourceExists( string $id ): bool {

            return $this->ApiClientQueryManager->resourceExists( $id );

        }

        /** @return Category\Category[] */
        public function getCategories(): array {

            $Request = ApiClient::createRequest( HttpMethodsEnum::get(), 'categories' );

            $Request->send();

            return map($Request->getResponse()->getAsJson(), static function($result ): Category\Category {

                return new Category\Category( $result['id'], $result['name'] );

            });

        }

        public function getAuthor( string $id ): Resource\Author {

            $result = $this->ApiClientQueryManager->getAuthor( $id );

            $Author = new Resource\Author( $result['name'] );

            if( !empty( $result['icon']['data'] ) ) {

                $Author->setIconUri( Uri::fromString('data:image/jpeg;charset=utf-8;base64,' . $result['icon']['data']) );

            }

            if( !empty( $result['icon']['url'] ) ) {

                $Author->setIconUri( Uri::fromString( $result['icon']['url'] ) );

            }

            return $Author;

        }

    }

?>
