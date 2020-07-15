<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\Client;

    use function \_\map;
    use function \_\find;
    use \TeamSpeak3_Node_Client as ApiClient;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;

    class Clients {

        private $Hypervisor;
        private $VirtualServer;

        /** @var Client[]|null */
        private $allClients = null;

        public function __construct( Hypervisor\Hypervisor $Hypervisor, VirtualServer\VirtualServer $VirtualServer ) {

            $this->Hypervisor = $Hypervisor;
            $this->VirtualServer = $VirtualServer;

        }

        /** @return Client[] */
        public function getAll(): array {

            if( $this->allClients === null ) {

                $this->allClients = map(

                    $this->VirtualServer->getApi()->clientList(),
                    function( ApiClient $Client ): Client {

                        return new Client($this->Hypervisor, $this->VirtualServer, $Client);

                    }

                );

            }

            return $this->allClients;

        }

        public function get( int $id ): Client {

            return find($this->getAll(), static function( Client $Client ) use ( $id ): bool { return $Client->getId() === $id; });

        }

        public function exists( string $id ): bool {

            return find($this->getAll(), static function( Client $Client ) use ( $id ): bool { return $Client->getId() === $id; }) !== null;

        }

        public function count(): int {

            return $this->VirtualServer->getApi()->clientCount();

        }

        public function getMax(): int {

            return $this->VirtualServer->getApi()->getProperty('virtualserver_maxclients');

        }

    }

?>
