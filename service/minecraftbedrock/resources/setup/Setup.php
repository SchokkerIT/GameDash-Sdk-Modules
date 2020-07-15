<?php

    namespace GameDash\Sdk\Module\Implementation\Service\MinecraftBedrock\Resources\Setup;

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

            $this->Instance->getFileSystem()->getRootDirectory()->getAllocation()->setSize( 150000 );

            $this->createNetworkPorts();

            $this->createServerProperties( $parameters['maxConnectedClients'] );

            $this->Instance->getInstaller()->getSources()->get('MinecraftBedrock')
                ->getResources()->get('default')->install();

            $this->Instance->getSettings()->create('maxConnectedClients', $parameters['maxConnectedClients']);

        }

        public function getInstallParameters(): array {

            return [

                'maxConnectedClients'

            ];

        }

        public function uninstall(): void {}


        private function createServerProperties( int $maxConnectedClients ): void {

            $Path = new Path\Path($this->Instance, 'server.properties');

            $this->FileSystem->getFiles()->get( $Path )->create();

            $Settings = $this->FileSystem->getConfigEditor()->getFiles()->get( $Path )->getSettings();

            $Settings->createInstance('server-name', $this->Instance->getName()->getValue());
            $Settings->createInstance('gamemode', 'survival');
            $Settings->createInstance('difficulty', 'easy');
            $Settings->createInstance('allow-cheats', 'false');
            $Settings->createInstance('max-players', $maxConnectedClients);
            $Settings->createInstance('online-mode', 'true');
            $Settings->createInstance('white-list', 'false');
            $Settings->createInstance('server-port', $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber());
            $Settings->createInstance('server-portv6', $this->Instance->getNetwork()->getPorts()->getByName('ipv6')->getNumber());
            $Settings->createInstance('view-distance', '32');
            $Settings->createInstance('tick-distance', '4');
            $Settings->createInstance('player-idle-timeout', '0');
            $Settings->createInstance('max-threads', '1');
            $Settings->createInstance('level-name', $this->Instance->getName()->getValue());
            $Settings->createInstance('level-seed', '');
            $Settings->createInstance('default-player-permission-level', 'member');
            $Settings->createInstance('texturepack-required', 'false');
            $Settings->createInstance('content-log-file-enabled', 'true');

            $Settings->commit();

        }

        private function createNetworkPorts(): void {

            $Primary = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Primary->setIsPrimary( true );

            $Query = $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            );

            $Query->setName('ipv6');

        }

    }

?>
