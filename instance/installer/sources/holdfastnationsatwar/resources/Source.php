<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\HoldfastNationsAtWar\Resources;

    use function \_\map;
    use function \_\find;
    use function \_\first;
    use \Electrum\Uri\Uri;
    use \Electrum\Pagination\Pagination;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance\Installer\Source\Resource\Result;
    use \GameDash\Sdk\FFI\Steam\App;

    class Source
        extends Implementation\Instance\Installer\Source\Source
        implements
            Implementation\Instance\Installer\Source\IWithResourceSearching,
            Implementation\Instance\Installer\Source\IWithResourceIcons
    {

        use Implementation\Instance\Installer\Source\UseCategoriesTrait;

        /** @var App\App */
        private $App;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->App = App\Apps::get(589290);

        }

        public function searchResources( string $query, Pagination $Pagination ): Result\PaginatedResult {

            $WorkshopResult = $this->App->getWorkshop()->getItems()->search( $query, $Pagination );

            $Result = new Result\PaginatedResult(

                map(

                    $WorkshopResult->getItems(),
                    function( App\Workshop\Item\Item $Item ): Resource\Resource {

                        return $this->workshopItemToResource( $Item );

                    }

                )

            );

                $Result->setPagination( new Result\Pagination( $Pagination->getPage(), $Pagination->getPerPage() ) );
                $Result->setIsLast( $WorkshopResult->isLast() );

            return $Result;

        }

        public function getResources( Pagination $Pagination ): Result\PaginatedResult {

            $WorkshopResult = $this->App->getWorkshop()->getItems()->getAll( $Pagination );

            $Result = new Result\PaginatedResult(

                map(

                    $WorkshopResult->getItems(),
                    function( App\Workshop\Item\Item $Item ): Resource\Resource {

                        return $this->workshopItemToResource( $Item );

                    }

                )

            );

                $Result->setIsLast( $WorkshopResult->isLast() );
                $Result->setPagination( new Result\Pagination( $Pagination->getPage(), $Pagination->getPerPage() ) );

            return $Result;

        }

        public function getResource( string $id ): Implementation\Instance\Installer\Source\Resource\Resource {

            $Item = $this->App->getWorkshop()->getItems()->get( $id );

            if( !$Item ) {

                return null;

            }

            return $this->workshopItemToResource( $Item );

        }

        public function resourceExists( string $id ): bool {

            try {

                $this->App->getWorkshop()->getItems()->get( $id );

                return true;

            }
            catch( App\Workshop\Item\ItemNotFoundException $Ignored ) {

                return false;

            }

        }

        private function workshopItemToResource( App\Workshop\Item\Item $Item ): Resource\Resource {

            $Resource = new Resource\Resource( $Item->getId() );

                $Resource->setTitle( $Item->getTitle() );

                if( $Item->getDescription() ) {

                    $Resource->setDescription( $Item->getDescription() );

                }

                /** @var App\Workshop\Item\Preview\ImagePreview $Preview */
                $Preview = find($Item->getPreviews(), static function( App\Workshop\Item\Preview\IPreview $Preview ): bool {

                    return $Preview instanceof App\Workshop\Item\Preview\ImagePreview;

                });

                if( $Preview ) {

                    $Resource->setIconUri( $Preview->getUrl() );

                }

                $Resource->setVersions(

                    map($Item->getVersions(), static function( App\Workshop\Item\Version\Version $ItemVersion ): Resource\Version {

                        $Version = new Resource\Version( $ItemVersion->getId() );

                            $Version->setIsLatest( $ItemVersion->isLatest() );

                        return $Version;

                    })

                );

                $Resource->setTimeCreated( $Item->getTimeCreated() );

            return $Resource;

        }

    }

?>
