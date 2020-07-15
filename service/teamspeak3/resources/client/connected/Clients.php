<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Resources\Client\Connected;

    use function \_\map;
    use function \_\find;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;
    use \GameDash\Sdk\FFI\Instance;

    class Clients extends Implementation\Service\Client\Connected\Clients {

        private $Gateway;
        private $VirtualServer;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $Hypervisor = new Hypervisor\Hypervisor( $Instance->getInfrastructure()->getNode() );

            $this->VirtualServer = $Hypervisor->getVirtualServers()->get( $Instance->getSettings()->get('teamspeak3.virtual_server.id')->getValue() );

        }

        /** @return Client[] */
        public function getAll(): array {

            return map($this->VirtualServer->getClients()->getAll(), function( VirtualServer\Client\Client $Client ): Client {

                return new Client( $this->Gateway, $Client->getId() );

            });

        }

        public function get( string $name ): Implementation\Service\Client\Connected\Client {

            return find($this->getAll(), static function( Client $Client ) use ( $name ): bool {

                return $Client->getName() === $name;

            });

        }

        public function isAvailable(): bool {

            return $this->VirtualServer->isOnline();

        }

    }

?>
