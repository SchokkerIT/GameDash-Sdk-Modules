<?php

    namespace GameDash\Sdk\Module\Implementation\Service\CounterStrikeSource\Resources\Setup;

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

            $this->Instance = new Instance\Instance( $Gateway->getParameters()->get('instance.id')->getValue() );
            $this->FileSystem = $this->Instance->getFileSystem();

        }

        public function install( array $parameters ): void {

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 150000 );

            $this->createNetworkPorts();

            $this->Instance->getInstaller()->getSources()->get('SteamCmd')
                ->getResources()->get('232330')->install();

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );

            $MapSetting = $this->Instance->getSettings()->create('map', 'de_dust2');
            $MapSetting->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $MapSetting->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $MapSetting->getForm()->getTypes()->get('Text')->setAsCurrent();
            $MapSetting->getForm()->setTitle('Map');

            $this->updateServerConfig();

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

            $Path = new Path\Path($this->Instance, 'cstrike/cfg/server.cfg');

            $this->FileSystem->getFiles()->get( $Path )->create();

            $this->FileSystem->getFiles()->get( $Path )->write(

                ( new Electrum\FileSystem\File\File(

                    new Electrum\FileSystem\Path\Path( __DIR__ . '/server.cfg' ) )

                )->read()

            );

            $Settings = $this->FileSystem->getConfigEditor()->getFiles()->get( $Path )->getSettings();

            $Settings->getFirst('hostname')->setValue( '"' . $this->Instance->getName()->getValue() . '"' );

            $Settings->commit();

        }

        private function createNetworkPorts(): void {

            $Primary = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Primary->setIsPrimary( true );

        }

    }

?>
