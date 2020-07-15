<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Resources\Statistic;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;

    class Statistics extends Implementation\Service\Statistic\Statistics {

        private $VirtualServer;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $Hypervisor = new Hypervisor\Hypervisor( $Instance->getInfrastructure()->getNode() );

            $this->VirtualServer = $Hypervisor->getVirtualServers()->get( $Instance->getSettings()->get('teamspeak3.virtual_server.id')->getValue() );

        }

        public function countConnectedClients(): int {

            return $this->VirtualServer->getClients()->count();

        }

        public function getMaxConnectedClients(): int {

            return $this->VirtualServer->getClients()->getMax();

        }

    }

?>
