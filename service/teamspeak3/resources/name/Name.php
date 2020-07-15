<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Resources\Name;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Setup\StateEnum;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;

    class Name extends Implementation\Service\Name\Name {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Hypervisor\Hypervisor */
        private $Hypervisor;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = new Instance\Instance( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Hypervisor = new Hypervisor\Hypervisor( $this->Instance->getInfrastructure()->getNode() );

        }

        public function set( string $name ): void {

            if( $this->Instance->getSetup()->getState()->compare( StateEnum::completed() ) && $this->Instance->getProcess()->getStatus()->isOnline() ) {

                $VirtualServer = $this->Hypervisor->getVirtualServers()->get($this->Instance->getSettings()->get('teamspeak3.virtual_server.id')->getValue());

                $VirtualServer->getApi()->modify([

                    'virtualserver_name' => $name

                ]);

            }

        }

    }

?>
