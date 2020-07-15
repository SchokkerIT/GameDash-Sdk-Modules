<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;

    use function \_\find;
    use function \_\map;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;

    class VirtualServers {

        private $Hypervisor;

        /** @var VirtualServer[] */
        private $virtualServers = [];

        public function __construct( Hypervisor\Hypervisor $Hypervisor ) {

            $this->Hypervisor = $Hypervisor;

        }

        /** @return VirtualServer[] */
        public function getAll(): array {

            try {

                return map($this->Hypervisor->getConnection()->getInstance()->serverList(), function( \TeamSpeak3_Node_Server $Server ): VirtualServer {

                    return $this->get($Server->getId());

                });

            }
            catch( \Exception $e ) {

                if( $e->getMessage() === 'database empty result set' ) {

                    return [];

                }

                throw $e;

            }

        }

        public function get( int $id ): VirtualServer {

            $VirtualServer = find($this->virtualServers, static function( VirtualServer $VirtualServer ) use ( $id ): bool { return $VirtualServer->getId() === $id; });

            if( !$VirtualServer ) {

                $VirtualServer = $this->virtualServers[] = new VirtualServer( $this->Hypervisor, $id );

            }

            return $VirtualServer;

        }

        public function exists( int $id ): bool {

            return find($this->getAll(), static function( VirtualServer $VirtualServer ) use ( $id ): bool { return $VirtualServer->getId() === $id; }) !== null;

        }

    }

?>
