<?php

    namespace GameDash\Sdk\Module\Implementation\Service\GarrysMod\Resources\Setup;

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

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 150000 );

            $this->createNetworkPorts();

            $this->Instance->getInstaller()->getSources()->get('SteamCmd')
                ->getResources()->get('4020')->install();

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );

            $Map = $this->Instance->getSettings()->create('map', 'gm_flatgrass');

            $Map->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $Map->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $Map->getForm()->getTypes()->get('Text')->setAsCurrent();
            $Map->getForm()->setTitle('Map');

            $AuthKey = $this->Instance->getSettings()->create('authKey', '');

            $AuthKey->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $AuthKey->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $AuthKey->getForm()->getTypes()->get('Text')->setAsCurrent();
            $AuthKey->getForm()->setTitle('Auth key');

            $WorkshopCollectionId = $this->Instance->getSettings()->create('workshopCollectionId', '');

            $WorkshopCollectionId->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $WorkshopCollectionId->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $WorkshopCollectionId->getForm()->getTypes()->get('Text')->setAsCurrent();
            $WorkshopCollectionId->getForm()->setTitle('Workshop Collection ID');

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

            $Path = new Path\Path($this->Instance, 'garrysmod/cfg/server.cfg');

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
