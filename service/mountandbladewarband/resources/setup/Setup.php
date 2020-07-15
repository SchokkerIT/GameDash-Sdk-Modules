<?php

    namespace GameDash\Sdk\Module\Implementation\Service\MountAndBladeWarband\Resources\Setup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Infrastructure\Node;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = new Instance\Instance( $Gateway->getParameters()->get('instance.id')->getValue() );
            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function install( array $parameters ): void {

            $this->createNetworkPorts();

            $this->Instance->getSettings()->create(

                'maxConnectedClients', $parameters['maxConnectedClients']

            );

            $ModuleSetting = $this->Instance->getSettings()->create('module', 'Native');
            $ModuleSetting->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $ModuleSetting->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $ModuleSetting->getForm()->getTypes()->get('Select')->setAsCurrent();
            $ModuleSetting->getForm()->setTitle('Module');
            $ModuleSetting->getForm()->getPlugins()->get('DirectoryContents')->add([

                'directories' => ['/Modules'],
                'useFullFilePath' => false,
                'includeDirectories' => true

            ]);

            $ConfigSetting = $this->Instance->getSettings()->create('config', 'config.txt');
            $ConfigSetting->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $ConfigSetting->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $ConfigSetting->getForm()->getTypes()->get('Select')->setAsCurrent();
            $ConfigSetting->getForm()->setTitle('Config file');
            $ConfigSetting->getForm()->getPlugins()->get('DirectoryContents')->add([

                'extensions' => ['txt'],
                'directories' => ['/'],
                'useFullFilePath' => false

            ]);

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 150000 );

            $Source = $this->Instance->getInstaller()->getSources()->get('Http');

            $Source->getResources()->get('mountandbladewarband')->install();

            $User = $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

            $WineWorkspace = $this->Node->getFileSystem()->getWorkspaces()->create();

            $WineWorkspace->setIsPermanent( true );

            $User->getFileSystem()->getDirectories()->add(

                'wine', $WineWorkspace->getFile()

            );

        }

        public function uninstall(): void {

            if( $this->Instance->getInfrastructure()->getNode()->getUsers()->hasCurrent() ) {

                $this->Instance->getInfrastructure()->getNode()->getUsers()->getCurrent()->delete();

            }

        }

        /** @return Instance\Setup\Install\Parameter\Parameter[] */
        public function getInstallParameters(): array {

            return [

                new Instance\Setup\Install\Parameter\NumericParameter('maxConnectedClients', 'Max connected clients', 'Amount of clients that connect at the same time')

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

            $Primary = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Primary->setIsPrimary( true );

        }

    }

?>
