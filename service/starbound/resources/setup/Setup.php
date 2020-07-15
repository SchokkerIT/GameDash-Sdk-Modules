<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Starbound\Resources\Setup;

    use \Electrum;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function install( array $parameters ): void {

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 100000 );

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );

            $this->createNetworkPorts();

            $this->Instance->getInstaller()->getSources()->get('SteamCmd')
                ->getResources()->get('211820')->install();

            $this->createConfigFile();

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

                })

            ];

        }

        public function uninstall(): void {}

        private function createConfigFile(): void {

            $ConfigFile = $this->Instance->getFileSystem()->getFiles()->get(

                new FileSystem\Path\Path( $this->Instance, 'storage/starbound_server.config' )

            );

            $ConfigFile->create();

            $ConfigFile->write(

                ( new Electrum\FileSystem\File\File(

                    new Electrum\FileSystem\Path\Path( __DIR__ . '/defaultConfig.json' ) )

                )->read()

            );

            $Settings = $this->Instance->getFileSystem()->getConfigEditor()->getFiles()->get( $ConfigFile->getPath() )
                ->getSettings();

            $Settings->getFirst('maxPlayers')->setValue($this->Instance->getSettings()->get('maxConnectedClients')->getValue());
            $Settings->getFirst('serverName')->setValue($this->Instance->getName()->getValue());
            $Settings->getFirst('gameServerPort')->setValue($this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber());
            $Settings->getFirst('runQueryServer')->setValue(true);
            $Settings->getFirst('queryServerPort')->setValue($this->Instance->getNetwork()->getPorts()->getByName('query')->getNumber());
            $Settings->getFirst('rconServerPort')->setValue($this->Instance->getNetwork()->getPorts()->getByName('rcon')->getNumber());

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

            $Query->setName('query');

            $Query = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Query->setName('rcon');

        }

    }

?>
