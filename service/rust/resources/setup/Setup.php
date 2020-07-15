<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Rust\Resources\Setup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Company\Company;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = new Instance\Instance( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function install( array $parameters ): void {

            $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 5000 );

            $this->Instance->getInstaller()->getSources()->get('SteamCmd')
                ->getResources()->get('258550')->install();

            $this->createNetworkPorts();

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );

            $this->Instance->getSettings()->create('rcon_password', $parameters['rcon_password']);
            $this->Instance->getSettings()->create('level', 'Procedural Map');
            $this->Instance->getSettings()->create('description', 'This server is hosted by ' . Company::getTradingName());
            $this->Instance->getSettings()->create('header_image', '');
            $this->Instance->getSettings()->create('server_url', '');

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

            $Query = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Query->setName('rcon');
        }

    }

?>
