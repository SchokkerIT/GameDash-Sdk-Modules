<?php

    namespace GameDash\Sdk\Module\Implementation\Service\MountAndBladeWarband\Event\Listeners\Installer\Source\Resource;

    use \Electrum;
    use \Electrum\FileSystem as LocalFileSystem;
    use \Electrum\Userland\Sdk\Event\Listener\IListener;
    use \GameDash\Sdk\FFI\Company;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Installer\Source;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path\Path;

    class Install implements IListener {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Source\Source */
        private $Source;

        /** @var Source\Resource\Resource */
        private $Resource;

        public function __construct( array $args ) {

            $this->Instance = Instance\Instances::get( $args['instance.id'] );
            $this->Source = $this->Instance->getInstaller()->getSources()->get( $args['source.name'] );
            $this->Resource = $this->Source->getResources()->get( $args['source.resource.id'] );

        }

        public function execute(): void {

            if( $this->Source->getName() === 'Http' ) {

                if($this->Resource->getId() === 'mountandbladewarband_native') {

                    $TemplateFile = new LocalFileSystem\File\File( new Electrum\FileSystem\Path\Path( __DIR__ . '/templates/native.txt') );

                    if( !$this->Instance->getFileSystem()->getFiles()->get( new Path($this->Instance, 'config.txt') )->exists() ) {

                        $this->createConfigFile(

                            new Path($this->Instance, 'config.txt'),

                            $TemplateFile->read()

                        );

                    }

                    $this->Instance->getSettings()->get('module')->setValue('Native');

                    $this->Instance->getSettings()->get('config')->setValue('config.txt');

                }
                else if($this->Resource->getId() === 'mountandbladewarband_nw') {

                    $TemplateFile = new LocalFileSystem\File\File( new Electrum\FileSystem\Path\Path( __DIR__ . '/templates/nw_config.txt') );

                    if( !$this->Instance->getFileSystem()->getFiles()->get( new Path($this->Instance, 'nw_config.txt') )->exists() ) {

                        $this->createConfigFile(

                            new Path($this->Instance, 'nw_config.txt'),

                            $TemplateFile->read()

                        );

                    }

                    $this->Instance->getSettings()->get('module')->setValue('Napoleonic Wars');
                    $this->Instance->getSettings()->get('config')->setValue('nw_config.txt');

                }

            }

        }

        private function createConfigFile( Path $Path, string $template ): void {

            $File = $this->Instance->getFileSystem()->getFiles()->get(

                new Path( $this->Instance, $Path->toString() )

            );

            $File->create();

            $File->write( $template );

            $Settings = $this->Instance->getFileSystem()->getConfigEditor()->getFiles()->get( $File->getPath() )->getSettings();

            $maxConnectedClients = $this->Instance->getSettings()->get('maxConnectedClients')->getValue();

            $Settings->getFirst('set_server_name')->setValue($this->Instance->getName()->getValue());
            $Settings->getFirst('set_welcome_message')->setValue('Server hosted by ' . Company\Company::getTradingName());
            $Settings->getFirst('set_max_players')->setValue($maxConnectedClients . ' ' . $maxConnectedClients);
            $Settings->getFirst('set_port')->setValue($this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber());

            $Settings->commit();

        }

    }

?>
