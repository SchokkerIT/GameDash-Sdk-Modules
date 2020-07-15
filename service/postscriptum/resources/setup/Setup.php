<?php

    namespace GameDash\Sdk\Module\Implementation\Service\PostScriptum\Resources\Setup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Company;
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

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 10000 );

            $this->createNetworkPorts();

            $this->Instance->getInstaller()->getSources()->get('SteamCmd')
                ->getResources()->get('746200')->install();

            $this->Instance->getSettings()->create('maxConnectedClients', $parameters['maxConnectedClients']);

            $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

            $this->updateConfigFile();
            $this->updateMOTD();
            $this->updateServerMessages();

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

        private function createNetworkPorts(): void {

            if( !$this->Instance->getNetwork()->getPorts()->getPrimary() ) {

                $Primary = $this->Instance->getNetwork()->getPorts()->create(

                    $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()->getNumber()

                );

                $Primary->setIsPrimary(true);

            }

            if( !$this->Instance->getNetwork()->getPorts()->existsByName('query') ) {

                $Query = $this->Instance->getNetwork()->getPorts()->create(

                    $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

                );

                $Query->setName('query');

            }

            if( !$this->Instance->getNetwork()->getPorts()->existsByName('rcon') ) {

                $Query = $this->Instance->getNetwork()->getPorts()->create(

                    $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

                );

                $Query->setName('rcon');

            }

        }

        private function updateConfigFile(): void {

            $Path = new Path\Path($this->Instance, 'PostScriptum/ServerConfig/Server.cfg');

            $Settings = $this->FileSystem->getConfigEditor()->getFiles()->get( $Path )->getSettings();

            $Settings->getFirst('MaxPlayers')->setValue( $this->Instance->getSettings()->get('maxConnectedClients')->getValue() );

            $Settings->commit();

        }

        private function updateMOTD(): void {

            $File = $this->Instance->getFileSystem()->getFiles()->get(

                new Path\Path($this->Instance, 'PostScriptum/ServerConfig/MOTD.cfg')

            );

            $File->write('Server hosted by ' . Company\Company::getTradingName());

        }

        private function updateServerMessages(): void {

            $File = $this->Instance->getFileSystem()->getFiles()->get(

                new Path\Path($this->Instance, 'PostScriptum/ServerConfig/ServerMessages.cfg')

            );

            $File->write('Server hosted by ' . Company\Company::getTradingName());

        }

    }

?>
