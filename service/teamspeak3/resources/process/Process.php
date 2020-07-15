<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;
    use \GameDash\Sdk\FFI\Instance;

    class Process extends Implementation\Service\Process\Process {

        private $Hypervisor;
        private $VirtualServer;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Hypervisor = new Hypervisor\Hypervisor( $Instance->getInfrastructure()->getNode() );

            $this->VirtualServer = $this->Hypervisor->getVirtualServers()->get( $Instance->getSettings()->get('teamspeak3.virtual_server.id')->getValue() );

        }

        public function start(): void {

            $this->VirtualServer->start();

        }

        public function stop(): void {

            $this->VirtualServer->stop();

        }

        public function restart(): void {}

        public function isOnline(): bool {

            return $this->Hypervisor->isOnline() && $this->VirtualServer->isOnline();

        }

        public function usageIsMeasurable(): bool {

            return false;

        }

    }

?>
