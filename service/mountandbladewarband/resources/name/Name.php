<?php

    namespace GameDash\Sdk\Module\Implementation\Service\MountAndBladeWarband\Resources\Name;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;

    class Name extends Implementation\Service\Name\Name {

        /** @var Instance\Instance */
        private $Instance;

        /** @var FileSystem\FileSystem */
        private $FileSystem;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );
            $this->FileSystem = $this->Instance->getFileSystem();

        }

        public function set( string $name ): void {

            if( !$this->Instance->getSettings()->exists('config') ) {

                return;

            }

            $Config = $this->FileSystem->getConfigEditor()->getFiles()->get(

                new FileSystem\Path\Path(

                    $this->Instance,
                    $this->Instance->getSettings()->get('config')->getValue()

                )

            );

            if( $Config->isAllowed() ) {

                $Settings = $Config->getSettings();

                if( !$Settings->exists('set_server_name') ) {

                    $Settings->createInstance('set_server_name', preg_replace('/\s+/', '_', $name));

                }
                else {

                    $Settings->getFirst('set_server_name')->setValue( $name );

                }

                $Settings->commit();

            }

        }

    }

?>
