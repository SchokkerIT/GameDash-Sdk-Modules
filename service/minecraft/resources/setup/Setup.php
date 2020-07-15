<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Setup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Company;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Installer;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Dependency;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class Setup extends Implementation\Service\Setup\Setup {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Node\Node */
        private $Node;

        /** @var FileSystem\FileSystem */
        private $FileSystem;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->FileSystem = $this->Instance->getFileSystem();

            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function install( array $parameters ): void {

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 10000 );

            $this->allocateNetworkPorts();

            $this->createServerProperties(

                self::getScaledMaxClientsValue( $parameters['ram_mb'] )

            );

            $this->installDefaultJar();

            $PrimaryJdkVersion = $this->getPrimaryJdkVersion();

            if( $PrimaryJdkVersion ) {

                $JavaVersions = $this->Instance->getSettings()->create('java_version', $PrimaryJdkVersion->getName());

                $JavaVersions->getPermissions()->get('isViewableByClient')->setHasPermission( true );
                $JavaVersions->getPermissions()->get('isEditableByClient')->setHasPermission( true );
                $JavaVersions->getForm()->getTypes()->get('Select')->setAsCurrent();
                $JavaVersions->getForm()->setTitle('Java version');
                $JavaVersions->getForm()->getPlugins()->get('JavaVersions')->add([]);

            }

            $this->Instance->getSettings()->create('ram_mb', $parameters['ram_mb']);

            if( $this->Instance->getInfrastructure()->getNode()->getOperatingSystems()->getCurrent()->isLinux() ) {

                $this->Instance->getInfrastructure()->getNode()->getUsers()->create();

            }

        }

        public function uninstall(): void {}

        public function afterReset(): void {

            $Agreements = $this->Instance->getLegal()->getAgreements();

            if( $Agreements->exists('minecraft-server-eula') && $Agreements->isSigned('minecraft-server-eula') ) {

                $Agreements->getSigned('minecraft-server-eula')->delete();

            }

        }

        /** @return Instance\Setup\Install\Parameter\Parameter[] */
        public function getInstallParameters(): array {

            return [

                new Instance\Setup\Install\Parameter\NumericParameter('ram_mb', 'MB RAM', 'Amount of RAM in MB allocated')

            ];

        }

        /** @return Instance\Setup\Install\Parameter\Parameter[] */
        public function getInstallArguments(): array {

            return [

                (function() {

                    $Parameter = $this->Instance->getSetup()->getInstallParameter('ram_mb');

                        $Parameter->setValue(

                            $this->Instance->getSettings()->get('ram_mb')->getValue()

                        );

                    return $Parameter;

                })()

            ];

        }

        private function installDefaultJar(): void {

            /** @var Installer\Source\Resource\Resource $Resource */
            $Resource = null;

            if( $this->Instance->getInstaller()->getSources()->exists('MinecraftJarPaper') ) {

                $Source = $this->Instance->getInstaller()->getSources()->get('MinecraftJarPaper');

                $Resource = $Source->getResource('paper');

            }
            else {

                $Source = $this->Instance->getInstaller()->getSources()->get('MinecraftJarVanilla');

                $Resource = $Source->getResource('vanilla');

            }

            $ResourceInstallManagerResult = $Resource->getInstallManager()->install();

            $JarSetting = $this->Instance->getSettings()->create(

                'jar',
                $ResourceInstallManagerResult->getPrimaryRecord()->getFiles()[0]->getName()

            );

            $JarSetting->getPermissions()->get('isViewableByClient')->setHasPermission( true );
            $JarSetting->getPermissions()->get('isEditableByClient')->setHasPermission( true );
            $JarSetting->getForm()->getTypes()->get('Select')->setAsCurrent();
            $JarSetting->getForm()->setTitle('JAR file');
            $JarSetting->getForm()->getPlugins()->get('DirectoryContents')->add([

                'extensions' => ['jar'],
                'directories' => ['/']

            ]);

        }

        private function getPrimaryJdkVersion(): ?Dependency\Dependency {

            $Group = $this->Node->getDependencies()->getGroups()->get('jdk');

            if( !$Group ) {

                return null;

            }

            $Item = $Group->getPrimaryItem();

            return $Item ? $Item->getDependency() : null;

        }

        private function createServerProperties( int $maxConnectedClients ): void {

            $Path = new Path\Path($this->Instance, 'server.properties');

            $this->FileSystem->getFiles()->get( $Path )->create();

            $Settings = $this->FileSystem->getConfigEditor()->getFiles()->get( $Path )->getSettings();

            $Settings->add( $Settings->createInstance('generator-settings', '') );
            $Settings->add( $Settings->createInstance('force-gamemode', 'false') );
            $Settings->add( $Settings->createInstance('allow-nether', 'true') );
            $Settings->add( $Settings->createInstance('enforce-whitelist', 'false') );
            $Settings->add( $Settings->createInstance('gamemode', '0') );
            $Settings->add( $Settings->createInstance('broadcast-console-to-ops', 'true') );
            $Settings->add( $Settings->createInstance('enable-query', 'true') );
            $Settings->add( $Settings->createInstance('query.port', $this->Instance->getNetwork()->getPorts()->getByName('query')->getNumber()) );
            $Settings->add( $Settings->createInstance('player-idle-timeout', '0') );
            $Settings->add( $Settings->createInstance('difficulty', '1') );
            $Settings->add( $Settings->createInstance('spawn-monsters', 'true') );
            $Settings->add( $Settings->createInstance('op-permission-level', '4') );
            $Settings->add( $Settings->createInstance('pvp', 'true') );
            $Settings->add( $Settings->createInstance('snooper-enabled', 'true') );
            $Settings->add( $Settings->createInstance('level-type', 'DEFAULT') );
            $Settings->add( $Settings->createInstance('hardcore', 'false') );
            $Settings->add( $Settings->createInstance('enable-command-block', 'false') );
            $Settings->add( $Settings->createInstance('max-players', $maxConnectedClients) );
            $Settings->add( $Settings->createInstance('network-compression-threshold', '256') );
            $Settings->add( $Settings->createInstance('resource-pack-sha1', '') );
            $Settings->add( $Settings->createInstance('max-world-size', '29999984') );
            $Settings->add( $Settings->createInstance('server-port', $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber()) );
            $Settings->add( $Settings->createInstance('server-ip', '') );
            $Settings->add( $Settings->createInstance('spawn-npcs', 'true') );
            $Settings->add( $Settings->createInstance('allow-flight', 'false') );
            $Settings->add( $Settings->createInstance('level-name', 'world') );
            $Settings->add( $Settings->createInstance('view-distance', '10') );
            $Settings->add( $Settings->createInstance('resource-pack', '') );
            $Settings->add( $Settings->createInstance('spawn-animals', 'true') );
            $Settings->add( $Settings->createInstance('white-list', 'false') );
            $Settings->add( $Settings->createInstance('generate-structures', 'true') );
            $Settings->add( $Settings->createInstance('online-mode', 'true') );
            $Settings->add( $Settings->createInstance('max-build-height', '256') );
            $Settings->add( $Settings->createInstance('level-seed', '') );
            $Settings->add( $Settings->createInstance('prevent-proxy-connections', 'false') );
            $Settings->add( $Settings->createInstance('use-native-transport', 'true') );
            $Settings->add( $Settings->createInstance('enable-rcon', 'false') );
            $Settings->add( $Settings->createInstance('motd', 'Hosted by ' . Company\Company::getTradingName()) );

            $Settings->commit();

        }

        private function allocateNetworkPorts(): void {

            if( $this->Instance->getNetwork()->getPorts()->getPrimary() === null ) {

                $Primary = $this->Instance->getNetwork()->getPorts()->create(

                    $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()->getNumber()

                );

                $Primary->setIsPrimary(true);

            }

            if( !$this->Instance->getNetwork()->getPorts()->existsByName('query') ) {

                $Query = $this->Instance->getNetwork()->getPorts()->create(

                    $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()->getNumber()

                );

                $Query->setName('query');

            }

            $this->Instance->getNetwork()->getPorts()->allocate();

        }

        private static function getScaledMaxClientsValue( int $ram ): int {

            return floor( $ram / 32 );

        }

    }

?>
