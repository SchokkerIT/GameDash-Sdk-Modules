<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Terraria\Resources\Setup;

    use \Electrum;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = new Instance\Instance( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function install( array $parameters ): void {

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 100000 );

            $this->createNetworkPorts();

            $this->Instance->getInstaller()->getSources()->get('TShock')
                ->getResources()->get('default')->install();

            $this->Instance->getFileSystem()->getFiles()->get( new FileSystem\Path\Path( $this->Instance, 'tshock' ) )
                ->makeDirectory();

            $this->createConfigFile();

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );
            $this->Instance->getSettings()->create(

                'world', $parameters['world']

            );

            $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

        }

        /** @return Instance\Setup\Install\Parameter\Parameter[] */
        public function getInstallParameters(): array {

            return [

                new Instance\Setup\Install\Parameter\NumericParameter('maxConnectedClients', 'Max connected clients'),
                new Instance\Setup\Install\Parameter\StringParameter('world', 'World'),

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

                }),

                (function() {

                    $Parameter = $this->Instance->getSetup()->getInstallParameter('world');

                    $Parameter->setValue(

                        $this->Instance->getSettings()->get('world')->getValue()

                    );

                    return $Parameter;

                })

            ];

        }

        public function uninstall(): void {}

        private function createNetworkPorts(): void {

            $Primary = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Primary->setIsPrimary( true );

            $RestApi = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $RestApi->setName('restapi');

        }

        private function createConfigFile(): void {

            $TShockConfigPath = new FileSystem\Path\Path( $this->Instance, 'tshock/config.json' );

            $this->Instance->getFileSystem()->getFiles()->get( $TShockConfigPath )->create();

            $this->Instance->getFileSystem()->getFiles()->get( $TShockConfigPath )
                ->write(

                    ( new Electrum\FileSystem\File\File(

                        new Electrum\FileSystem\Path\Path( __DIR__ . '/defaultConfig.json' ) )

                    )->read()

                );

            $Settings = $this->Instance->getFileSystem()->getConfigEditor()->getFiles()->get( $TShockConfigPath )
                ->getSettings();

            $Settings->createInstance('RestApiEnabled', true);
            $Settings->createInstance('RestApiPort', $this->Instance->getNetwork()->getPorts()->getByName('restapi')->getNumber());

            $Settings->commit();

        }

    }

?>
