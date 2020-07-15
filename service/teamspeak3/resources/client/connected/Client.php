<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Resources\Client\Connected;

    use \Electrum\Base64\Base64;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;
    use \GameDash\Sdk\FFI\Instance;

    class Client extends Implementation\Service\Client\Connected\Client implements Implementation\Service\Client\Connected\IHasImage {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var VirtualServer\VirtualServer */
        private $VirtualServer;

        /** @var VirtualServer\Client\Client */
        private $Client;

        /** @var int */
        private $id;

        public function __construct( Gateway\Gateway $Gateway, int $id ) {

            $this->Gateway = $Gateway;

            $this->id = $id;

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $Hypervisor = new Hypervisor\Hypervisor( $Instance->getInfrastructure()->getNode() );

            $this->VirtualServer = $Hypervisor->getVirtualServers()->get( $Instance->getSettings()->get('teamspeak3.virtual_server.id')->getValue() );

            $this->Client = $this->VirtualServer->getClients()->get( $id );

        }

        public function getId(): string {

            return $this->id;

        }

        public function getName(): string {

            return $this->Client->getName();

        }

        public function getImage(): string {

            return Base64::encode( $this->Client->downloadAvatar() );

        }

        public function hasImage(): bool {

            return $this->Client->hasAvatar();

        }

        public function getActions(): array {

            $actions = [

                $this->Gateway->getHelpers()->get('createClientAction')->execute([

                    $this, 'kick', 'Kick', function() {

                        $this->Client->kick();

                    }

                ]),

                $this->Gateway->getHelpers()->get('createClientAction')->execute([

                    $this, 'ban', 'Ban', function() {

                        $this->Client->ban();

                    }

                ])

            ];

            $ServerAdminGroup = $this->VirtualServer->getGroups()->get(45);

            if( !$this->Client->hasGroup( $ServerAdminGroup ) ) {

                $actions[] = $this->Gateway->getHelpers()->get('createClientAction')->execute([

                    $this, 'add_server_admin', 'Make server admin', function() use ( $ServerAdminGroup ) {

                        $this->Client->addGroup( $ServerAdminGroup );

                    }

                ]);

            }
            else {

                $actions[] = $this->Gateway->getHelpers()->get('createClientAction')->execute([

                    $this, 'remove_server_admin', 'Remove server admin', function() use ( $ServerAdminGroup ) {

                        $this->Client->removeGroup( $ServerAdminGroup );

                    }

                ]);

            }

            return $actions;

        }

    }

?>
