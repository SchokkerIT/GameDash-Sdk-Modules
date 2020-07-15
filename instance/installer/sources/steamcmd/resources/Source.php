<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\SteamCmd\Resources;

    use \Electrum\Pagination\Pagination;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Steam\App;
    use \GameDash\Sdk\FFI\Instance\Installer\Source\Resource\Result;

    class Source extends Implementation\Instance\Installer\Source\Source {

        /** @var Gateway\Gateway */
        private $Gateway;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

        }

        public function getResources( Pagination $Pagination ): Result\PaginatedResult {

            return new Result\PaginatedResult([]);

        }

        public function count(): ?int {

            return 0;

        }

        public function getResource( string $id ): Implementation\Instance\Installer\Source\Resource\Resource {

            $App = App\Apps::get( $id );

            $Resource = new Resource\Resource( $this->Gateway, $id );

                $Resource->setTitle( $App->getName() );

            return $Resource;

        }

        public function resourceExists(string $id ): bool {

            return true;

        }

        public function isHidden(): bool {

            return true;

        }

    }

?>
