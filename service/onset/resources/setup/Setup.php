<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Onset\Resources\Setup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Address;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function install( array $parameters ): void {

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 25000 );

            $this->Instance->getInstaller()->getSources()->get('steamcmd')
                ->getResources()->get('1204170')->install();

            $this->createNetworkPorts();
            $this->updateConfigFile();

            $this->Instance->getSettings()->create('maxConnectedClients', $parameters['maxConnectedClients']);

            $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

        }

        public function uninstall(): void {

            if( $this->Instance->getInfrastructure()->getNode()->getUsers()->hasCurrent() ) {

                $this->Instance->getInfrastructure()->getNode()->getUsers()->getCurrent()->delete();

            }

        }

        /** @return Instance\Setup\Install\Parameter\Parameter[] */
        public function getInstallParameters(): array {

            return [

                new Instance\Setup\Install\Parameter\NumericParameter('maxConnectedClients', 'Max connected clients')

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

        private function updateConfigFile(): void {

            $File = $this->Instance->getFileSystem()->getConfigEditor()->getFiles()->get(

                new Path\Path( $this->Instance, 'server_config.json' )

            );

            $Settings = $File->getSettings();

                $Settings->getFirst('servername')->setValue( $this->Instance->getName()->getValue() );
                $Settings->getFirst('servername_short')->setValue( $this->Instance->getName()->getValue() );
                $Settings->getFirst('website_url')->setValue( ( Address\Addresses::exists('sales') ? Address\Addresses::get('sales') : Address\Addresses::getSelf() )->getUri()->toString() );
                $Settings->getFirst('ipaddress')->setValue( $this->Instance->getNetwork()->getIps()->getCurrent()->toString() );
                $Settings->getFirst('port')->setValue( $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber() );
                $Settings->getFirst('maxplayers')->setValue( $this->Instance->getSettings()->get('maxConnectedClients')->getValue() );

            $Settings->commit();

        }

        private function createNetworkPorts(): void {

            $ports = $this->Node->getNetwork()->getPorts()->getRandomFreeSubsequentPorts(3);

            $Primary = $this->Instance->getNetwork()->getPorts()->create( $ports[2] );

            $Primary->setIsPrimary(true);

            $Query = $this->Instance->getNetwork()->getPorts()->create( $ports[1] );

            $Query->setName('query');

            $File = $this->Instance->getNetwork()->getPorts()->create( $ports[0] );

            $File->setName('file');

        }

    }

?>
