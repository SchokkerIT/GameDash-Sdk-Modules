<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Mordhau\Resources\Setup;

    use \Electrum;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        /** @var FileSystem\FileSystem */
        private $FileSystem;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );
            $this->FileSystem = $this->Instance->getFileSystem();

        }

        public function install( array $parameters ): void {

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );

            $Source = $this->Instance->getInstaller()->getSources()->get('SteamCmd');

            $Source->getResources()->get('629800')->install();

            $this->createNetworkPorts();

            $this->createDefaultConfigFile( $parameters['maxConnectedClients'] );

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 5000 );

            $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

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

        public function uninstall(): void {}

        private function createDefaultConfigFile( int $maxConnectedClients ): void {

            $Path = new Path\Path($this->Instance, 'Mordhau/Saved/Config/LinuxServer/Game.ini');

            $this->FileSystem->getFiles()->get( $Path )->create();

            $this->FileSystem->getFiles()->get( $Path )->write(

                ( new Electrum\FileSystem\File\File(

                    new Electrum\FileSystem\Path\Path( __DIR__ . '/defaultConfig.ini' ) )

                )->read()

            );

            $Settings = $this->FileSystem->getConfigEditor()->getFiles()->get( $Path )->getSettings();

            $Settings->getFirst('MaxSlots')->setValue($maxConnectedClients);
            $Settings->getFirst('ServerName')->setValue($this->Instance->getName()->getValue());

            $Settings->commit();

        }

        private function createNetworkPorts(): void {

            $Primary = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Primary->setIsPrimary( true );

            $Query = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Query->setName('beacon');

            $Query = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Query->setName('query');

        }

    }

?>
