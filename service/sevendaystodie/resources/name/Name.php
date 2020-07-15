<?php

    namespace GameDash\Sdk\Module\Implementation\Service\SevenDaysToDie\Resources\Name;

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

            if( $this->Instance->getSettings()->exists('config') ) {

                $Config = $this->FileSystem->getConfigEditor()->getFiles()->get(

                    new FileSystem\Path\Path($this->Instance, $this->Instance->getSettings()->get('config')->getValue())

                );

                if( $Config->isAllowed() ) {

                    $Settings = $Config->getSettings();

                    if( !$Settings->exists('ServerName') ) {

                        $Settings->createInstance('ServerName', $name);

                    }
                    else {

                        $Settings->getFirst('ServerName')->setValue($name);

                    }

                    $Settings->commit();

                }

            }

        }

    }

?>
