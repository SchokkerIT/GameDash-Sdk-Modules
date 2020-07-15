<?php

    namespace GameDash\Sdk\Module\Implementation\Service\SevenDaysToDie\Resources\Setup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Company;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Instance\FileSystem\File;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = new Instance\Instance( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function install( array $parameters ): void {

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 150000 );

            $this->createNetworkPorts();

            $this->Instance->getInstaller()->getSources()->get('SteamCmd')
                ->getResources()->get('294420')->install();

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );

            $ConfigSetting = $this->Instance->getSettings()->create('config', 'serverconfig.xml');
            $ConfigSetting->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $ConfigSetting->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $ConfigSetting->getForm()->getTypes()->get('Select')->setAsCurrent();
            $ConfigSetting->getForm()->setTitle('Config file');
            $ConfigSetting->getForm()->getPlugins()->get('DirectoryContents')->add([

                'extensions' => ['xml'],
                'directories' => ['/'],
                'useFullFilePath' => false

            ]);

            $this->getProfileDirectory()->makeDirectory();

            $this->updateConfig();

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

        private function updateConfig(): void {

            $ConfigPath = new FileSystem\Path\Path(

                $this->Instance,
                $this->Instance->getSettings()->get('config')->getValue()

            );

            $Settings = $this->Instance->getFileSystem()->getConfigEditor()->getFiles()->get( $ConfigPath )
                ->getSettings();

            $Settings->getFirst('ServerPort')->setValue($this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber());
            $Settings->getFirst('ServerName')->setValue($this->Instance->getName()->getValue());
            $Settings->getFirst('ServerWebsiteURL')->setValue($this->Instance->getName()->getValue());
            $Settings->getFirst('ServerDescription')->setValue('Hosted by ' . Company\Company::getTradingName());
            $Settings->getFirst('ServerMaxPlayerCount')->setValue($this->Instance->getSettings()->get('maxConnectedClients')->getValue());
            $Settings->getFirst('GameName')->setValue($this->Instance->getId());
            $Settings->getFirst('TerminalWindowEnabled')->setValue('true');

            $ProfileDirectory = $this->getProfileDirectory();

            $Settings->createInstance('UserDataFolder', $ProfileDirectory->getPath()->getAbsolute()->toString());
            $Settings->createInstance('SaveGameFolder', $ProfileDirectory->getPath()->getAbsolute()->toString());

            $Settings->commit();

        }

        private function initProfile(): void {

            $ProfileDirectory = $this->getProfileDirectory();

            $ProfileDirectory->makeDirectory();

        }

        private function createNetworkPorts(): void {

            $Primary = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Primary->setIsPrimary( true );

            $ControlPanelPort = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $ControlPanelPort->setName('control_panel');

            $TelnetPort = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $TelnetPort->setName('telnet');

        }

        private function getProfileDirectory(): File\File {

            return $this->Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $this->Instance, 'profile' )

            );

        }

    }

?>
