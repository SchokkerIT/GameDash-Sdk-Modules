<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Factorio\Resources\Setup;

    use \Electrum;
    use \Electrum\Utilities;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Company;
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

            $this->createNetworkPorts();

            $this->Instance->getInstaller()->getSources()->get('SteamCmd')
                ->getResources()->get('427520')->install();

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );
            $this->Instance->getSettings()->create('config', './data/server-settings.json');
            $this->Instance->getSettings()->create('save', './saves/default.zip');
            $this->Instance->getSettings()->create('rconPassword', Utilities\Random::generateString( 16 ));

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

        private function createNetworkPorts(): void {

            $Primary = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Primary->setIsPrimary( true );

            $Rcon = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Rcon->setName('rcon');

        }

        private function createConfigFile(): void {

            $ServerSettingsFile = $this->Instance->getFileSystem()->getFiles()->get(

                new FileSystem\Path\Path( $this->Instance, $this->Instance->getSettings()->get('config')->getValue() )

            );

            $ServerSettingsFile->create();

            $ServerSettingsFile->write(

                ( new Electrum\FileSystem\File\File(

                    new Electrum\FileSystem\Path\Path( __DIR__ . '/server-settings.json' ) )

                )->read()

            );

            $Settings = $this->Instance->getFileSystem()->getConfigEditor()->getFiles()->get( $ServerSettingsFile->getPath() )
                ->getSettings();

            $Settings->getFirst('name')->setValue( $this->Instance->getName()->getValue() );
            $Settings->getFirst('description')->setValue( 'Hosted by ' . Company\Company::getTradingName() );
            $Settings->getFirst('max_players')->setValue( $this->Instance->getSettings()->get('maxConnectedClients')->getValue() );

            $Settings->commit();

        }

    }

?>
