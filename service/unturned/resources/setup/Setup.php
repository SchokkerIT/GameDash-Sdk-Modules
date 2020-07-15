<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Unturned\Resources\Setup;

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

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 50000 );

            $this->createNetworkPorts();

            $this->Instance->getInstaller()->getSources()->get('SteamCmd')
                ->getResources()->get('1110390')->install();

            $this->Instance->getSettings()->create('maxConnectedClients', $parameters['maxConnectedClients']);

            $this->createModeSetting();

            $this->createDefaultServer();

            $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

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

        private function createDefaultServer(): void {

            $Directory = $this->FileSystem->getFiles()->get( new Path\Path( $this->Instance, 'Servers/primary' ) );

            if( !$Directory->exists() ) {

                $Directory->makeDirectory();

                $this->createDefaultServerCommandsFile($this->Instance->getName()->getValue());

            }

        }

        private function createDefaultServerCommandsFile( string $name ): void {

            $Path = new Path\Path($this->Instance, 'Servers/primary/Server/commands.dat');

            $this->FileSystem->getFiles()->get( $Path )->create();

            $Settings = $this->FileSystem->getConfigEditor()->getFiles()->get( $Path )->getSettings();

            $port = $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber();

            $Settings->createInstance('name', $name);
            $Settings->createInstance('port', $port);
            $Settings->createInstance('maxplayers', $this->Instance->getSettings()->get('maxConnectedClients')->getValue());
            $Settings->createInstance('welcome', 'Hosted by ' . Company\Company::getTradingName());

            $Settings->commit();

        }

        private function createNetworkPorts(): void {

            $ports = $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFreeSubsequentPorts( 3 );

            if( !$this->Instance->getNetwork()->getPorts()->getPrimary() ) {

                $Primary = $this->Instance->getNetwork()->getPorts()->create( $ports[0] );

                $Primary->setIsPrimary(true);

            }

            if( !$this->Instance->getNetwork()->getPorts()->existsByName('query') ) {

                $Query = $this->Instance->getNetwork()->getPorts()->create( $ports[1] );

                $Query->setName('query');

            }

            if( !$this->Instance->getNetwork()->getPorts()->existsByName('spare') ) {

                $Query = $this->Instance->getNetwork()->getPorts()->create( $ports[2] );

                $Query->setName('spare');

            }

        }

        private function createModeSetting(): void {

            $ModeSetting = $this->Instance->getSettings()->create('mode', 'secureserver');

            $ModeSetting->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $ModeSetting->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $ModeSetting->getForm()->getTypes()->get('Select')->setAsCurrent();
            $ModeSetting->getForm()->setTitle('Mode');
            $ModeSetting->getForm()->getTypes()->get('Select')->getInstance()->setOptions([

                [

                    'title' => 'Secure',
                    'value' => 'secureserver'

                ],

                [

                    'title' => 'Unsecure',
                    'value' => 'unsecureserver'

                ]

            ]);

        }

    }

?>
