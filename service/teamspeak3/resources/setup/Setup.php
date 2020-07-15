<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Resources\Setup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;
    use \GameDash\Sdk\FFI\Company;
    use \GameDash\Sdk\FFI\Instance;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Setting\Settings */
        private $Settings;

        /** @var Hypervisor\Hypervisor */
        private $Hypervisor;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Settings = $this->Instance->getSettings();

            $this->Hypervisor = new Hypervisor\Hypervisor( $this->Instance->getInfrastructure()->getNode() );

        }

        public function install( array $parameters ): void {

            if( !$this->Instance->getNetwork()->getPorts()->getPrimary() ) {

                $Primary = $this->Instance->getNetwork()->getPorts()->create(

                    $this->Instance->getNetwork()->getPorts()->getRandomFree()->getNumber()

                );

                $Primary->setIsPrimary(true);

            }
            else {

                $Primary = $this->Instance->getNetwork()->getPorts()->getPrimary();

            }

            $this->Settings->create('maxConnectedClients', $parameters['maxConnectedClients']);

            $data = $this->Hypervisor->getConnection()->getInstance()->serverCreate([

                'virtualserver_name' => $this->Instance->getName()->getValue(),
                'virtualserver_maxclients' => $this->Settings->get('maxConnectedClients')->getValue(),
                'virtualserver_port' => $Primary->getNumber(),
                'virtualserver_welcomemessage' => 'Server is hosted by ' . Company\Company::getTradingName()

            ]);

            $id = $data['sid'];

            $this->Instance->getSettings()->create('teamspeak3.virtual_server.id', $id);

            $VirtualServer = $this->Hypervisor->getVirtualServers()->get( $id );

            $Group = $VirtualServer->getGroups()->getByName('Server Admin');

            $Group->removePermission( 'b_virtualserver_modify_maxclients' );
            $Group->removePermission( 'i_needed_modify_power_virtualserver_modify_maxclients' );
            $Group->removePermission( 'b_virtualserver_snapshot_deploy' );
            $Group->removePermission( 'b_virtualserver_snapshot_create' );
            $Group->removePermission( 'i_needed_modify_power_virtualserver_snapshot_deploy' );
            $Group->removePermission( 'i_needed_modify_power_virtualserver_snapshot_create' );
            $Group->removePermission( 'b_virtualserver_modify_port' );

            $privilegeKeys = $VirtualServer->getPrivilegeKeys()->getAll();

            $EmailSender = $this->Instance->getClient()->getEmail()->createSender();

            $EmailSender->setTitle('Privilege key for your TeamSpeak 3 instance');
            $EmailSender->setMessage('To get added to the server admin group on your TeamSpeak 3 instance, enter privilege key "' . $privilegeKeys[0]->getKey() . '" when joining via the TeamSpeak 3 client');

            $EmailSender->send();

            $VirtualServer->stop();

        }

        public function uninstall(): void {

            if( $this->Hypervisor->getVirtualServers()->exists( $this->Settings->get('teamspeak3.virtual_server.id')->getValue() ) ) {

                if(!$this->getVirtualServer()->isOnline()) {

                    $this->getVirtualServer()->start();

                }

                $this->getVirtualServer()->delete();

            }

        }

        /** @return Instance\Setup\Install\Parameter\Parameter[] */
        public function getInstallParameters(): array {

            return [

                new Instance\Setup\Install\Parameter\NumericParameter('maxConnectedClients', 'Max connected clients', 'Amount of clients that connect at the same time')

            ];

        }

        /** @return Instance\Setup\Install\Parameter\Parameter[] */
        public function getInstallArguments(): array {

            return [

                (function() {

                    $Parameter = $this->Instance->getSetup()->getInstallParameter('maxConnectedClients');

                    $Parameter->setValue(

                        $this->Instance->getSettings()->get('maxConnectedClients')->getValue()

                    );

                    return $Parameter;

                })()

            ];

        }

        public function beforeReset(): void {}

        public function afterReset(): void {}

        private function getVirtualServer(): VirtualServer\VirtualServer {

            return $this->Hypervisor->getVirtualServers()->get( $this->Settings->get('teamspeak3.virtual_server.id')->getValue() );

        }

    }

?>
