<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Starbound\Resources\Network;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Ip\Ip;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;
    use \GameDash\Sdk\FFI\Instance\FileSystem\ConfigEditor;

    class Network extends Implementation\Service\Network\Network implements Implementation\Service\Network\ICanAllocateIp {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Instance\Instance */
        private $Instance;

        /** @var ConfigEditor\ConfigEditor */
        private $ConfigEditor;

        /** @var Path\Path */
        private $ServerPropertiesPath;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = new FFI\Instance\Instance( $this->Gateway->getParameters()->get('instance.id')->getValue() );
            $this->ConfigEditor = $this->Instance->getFileSystem()->getConfigEditor();

            $this->ServerPropertiesPath = new Instance\FileSystem\Path\Path( $this->Instance, 'server.properties' );

        }

        public function canAllocateIp(): bool {

            return true;

        }

        public function allocateIp( Ip $Ip ): void {

            $Settings = $this->ConfigEditor->getFiles()->get( $this->ServerPropertiesPath )->getSettings();

            $Settings->getFirst('gameServerBind')->setValue( $Ip->toString() );

            $Settings->commit();

        }

        public function unallocateIp(): void {

            $Settings = $this->ConfigEditor->getFiles()->get( $this->ServerPropertiesPath )->getSettings();

            $Settings->getFirst('gameServerBind')->setValue( $this->Instance->getNetwork()->getIps()->getCurrent()->toString() );

            $Settings->commit();

        }

    }

?>
