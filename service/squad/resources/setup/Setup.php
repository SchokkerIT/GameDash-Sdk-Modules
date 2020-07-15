<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Squad\Resources\Setup;

    use \Electrum;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Company;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Instance\Instance */
        private $Instance;

        /** @var FileSystem\FileSystem */
        private $FileSystem;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = new Instance\Instance( $this->Gateway->getParameters()->get('instance.id')->getValue() );
            $this->FileSystem = $this->Instance->getFileSystem();

        }

        public function install( array $parameters ): void {

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 175000 );

            $this->createNetworkPorts();

            $this->Instance->getInstaller()->getSources()->get('SteamCmd')
                ->getResources()->get('403240')->install();

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );

            $this->updateServerConfig();

            $this->updateMOTD();
            $this->updateServerMessages();

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

        private function updateServerConfig(): void {

            $Path = new Path\Path($this->Instance, 'SquadGame/ServerConfig/Server.cfg');

            $File = $this->FileSystem->getConfigEditor()->getFiles()->get( $Path );

            if( !$File->exists() ) {

                $File->create();

            }

            $Settings = $File->getSettings();

            $Settings->getFirst('ServerName')->setValue( '"' . $this->Instance->getName()->getValue() . '"' );
            $Settings->remove('MaxPlayers');

            $Settings->commit();

        }

        private function updateMOTD(): void {

            $File = $this->Instance->getFileSystem()->getFiles()->get(

                new Path\Path($this->Instance, 'SquadGame/ServerConfig/MOTD.cfg')

            );

            $File->write('Server hosted by ' . Company\Company::getTradingName());

        }

        private function updateServerMessages(): void {

            $File = $this->Instance->getFileSystem()->getFiles()->get(

                new Path\Path($this->Instance, 'SquadGame/ServerConfig/ServerMessages.cfg')

            );

            $File->write('Server hosted by ' . Company\Company::getTradingName());

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
