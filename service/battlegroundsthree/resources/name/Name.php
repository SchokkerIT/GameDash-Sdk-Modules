<?php

    namespace GameDash\Sdk\Module\Implementation\Service\BattleGroundsThree\Resources\Name;

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

            $this->Instance = new Instance\Instance( $Gateway->getParameters()->get('instance.id')->getValue() );
            $this->FileSystem = $this->Instance->getFileSystem();

        }

        public function set( string $name ): void {

            $ServerProperties = $this->FileSystem->getConfigEditor()->getFiles()->get(

                new FileSystem\Path\Path($this->Instance, 'server.properties')

            );

            if( $ServerProperties->exists() ) {

                $Settings = $ServerProperties->getSettings();

                if( !$Settings->exists('motd') ) {

                    $Settings->createInstance('motd', $name);

                }
                else {

                    $Settings->getFirst('motd')->setValue($name);

                }

                $Settings->commit();

            }

        }

    }

?>
