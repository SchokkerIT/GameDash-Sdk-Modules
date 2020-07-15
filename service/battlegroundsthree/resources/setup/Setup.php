<?php

    namespace GameDash\Sdk\Module\Implementation\Service\BattleGroundsThree\Resources\Setup;

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

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 12500 );

            $this->Instance->getInstaller()->getSources()->get('steamcmd')
                ->getResources()->get('1057700')->install();

            $this->createNetworkPorts();
            $this->updateConfigFile();

            $this->Instance->getSettings()->create('maxConnectedClients', $parameters['maxConnectedClients']);

            $Map = $this->Instance->getSettings()->create('map', 'bg_plateau');

            $Map->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $Map->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $Map->getForm()->getTypes()->get('Text')->setAsCurrent();
            $Map->getForm()->setTitle('Map');

            $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

        }

        public function uninstall(): void {

            if( $this->Node->getUsers()->hasCurrent() ) {

                $this->Node->getUsers()->getCurrent()->delete();

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

                new Path\Path( $this->Instance, 'bg3/cfg/server.cfg' )

            );

            $Settings = $File->getSettings();

                $Settings->getFirst('hostname')->setValue( $this->Instance->getName()->getValue() );
                $Settings->getFirst('sv_logecho')->setValue('1');

            $Settings->commit();

        }

        private function createNetworkPorts(): void {

            $Primary = $this->Instance->getNetwork()->getPorts()->create(

                $this->Node->getNetwork()->getPorts()->getRandomFree()

            );

            $Primary->setIsPrimary(true);

        }

    }

?>
