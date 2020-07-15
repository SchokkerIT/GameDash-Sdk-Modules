<?php

    namespace GameDash\Sdk\Module\Implementation\Service\HoldfastNationsAtWar\Resources\Setup;

    use \Electrum;
    use Electrum\Uri\Uri;
    use \Electrum\Utilities;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Company;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Infrastructure\Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function install( array $parameters ): void {

            $this->createSettings( $parameters['maxConnectedClients'] );

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 25000 );

            $this->createNetworkPorts();

            $this->createDefaultConfigFiles();

            $this->downloadServerFiles();

            $this->installSteamApp();

            $this->initializeMods();

            $User = $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

            if( $this->Node->getOperatingSystems()->getCurrent()->isLinux() ) {

                $WineWorkspace = $this->Node->getFileSystem()->getWorkspaces()->create();

                $WineWorkspace->setIsPermanent( true );

                $User->getFileSystem()->getDirectories()->add(

                    'wine', $WineWorkspace->getFile()

                );

            }

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

        private function createSettings( int $maxConnectedClients ): void {

            $this->Instance->getSettings()->create('fps', 60);
            $this->Instance->getSettings()->create('maxConnectedClients', $maxConnectedClients);

            $ConfigSetting = $this->Instance->getSettings()->create('config', 'config.txt');

            $ConfigSetting->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $ConfigSetting->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $ConfigSetting->getForm()->getTypes()->get('Select')->setAsCurrent();
            $ConfigSetting->getForm()->setTitle('Config file');
            $ConfigSetting->getForm()->getPlugins()->get('DirectoryContents')->add([

                'extensions' => ['txt'],
                'directories' => ['/']

            ]);

        }

        private function installSteamApp(): void {

            $Source = $this->Instance->getInstaller()->getSources()->get('SteamCmd');

            $Source->getResource(589290)->getInstallManager()->install();

        }

        private function downloadServerFiles(): void {

            $File = $this->Instance->getFileSystem()->getFiles()->get( new Path\Path( $this->Instance, 'serverfiles.zip' ) );

            $File->downloadFrom(

                Uri::fromString('https://www.dropbox.com/sh/ppkfny3r9kcnz8x/AADiIXOrlAWPh-XbhPpimw0ja?dl=1')

            );

            $File->unzip( $this->Instance->getFileSystem()->getRootDirectory()->getFile() );

            $File->delete();

        }

        private function createNetworkPorts(): void {

            $Primary = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()->getNumber()

            );

            $Primary->setIsPrimary( true );

            $Query = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()->getNumber()

            );

            $Query->setName('query');

            $SteamCommunications = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()->getNumber()

            );

            $SteamCommunications->setName('steam_communications');

        }

        private function createDefaultConfigFiles(): void {

            $ConfigTemplateFile = new Electrum\FileSystem\File\File( new Electrum\FileSystem\Path\Path( __DIR__ . '/config/template/config.txt' ) );

            $this->createConfigFile(

                new Path\Path($this->Instance, 'config.txt'),
                $ConfigTemplateFile->read()

            );

        }

        private function createConfigFile( Path\Path $Path, string $template ): void {

            $File = $this->Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $this->Instance, $Path->toString() )

            );

            $File->create();

            $File->write(

                Utilities\StringVariables::insert(

                    $template, [

                        'server_name' => $this->Instance->getName()->getValue(),
                        'server_welcome_message' => 'Server hosted by ' . Company\Company::getTradingName(),
                        'maximum_players' => $this->Instance->getSettings()->get('maxConnectedClients')->getValue(),
                        'server_port' => $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber(),
                        'steam_communications_port' => $this->Instance->getNetwork()->getPorts()->getByName('steam_communications')->getNumber(),
                        'steam_query_port' => $this->Instance->getNetwork()->getPorts()->getByName('query')->getNumber()

                    ]

                )

            );

        }

        private function initializeMods(): void {

            $RootDirectory = $this->Instance->getFileSystem()->getRootDirectory()->getFile();

            $ModsDirectory = $this->Instance->getFileSystem()->getFiles()->get( $RootDirectory->getPath()->createClone()->join('mods') );

            $ModsDirectory->makeDirectory();

        }

    }

?>
