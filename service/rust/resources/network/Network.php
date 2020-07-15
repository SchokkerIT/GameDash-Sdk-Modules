<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Rust\Resources\Network;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Ip\Ip;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\ConfigEditor;

    class Network extends Implementation\Service\Network\Network {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Instance\Instance */
        private $Instance;

        /** @var ConfigEditor\ConfigEditor */
        private $ConfigEditor;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = new FFI\Instance\Instance( $this->Gateway->getParameters()->get('instance.id')->getValue() );
            $this->ConfigEditor = $this->Instance->getFileSystem()->getConfigEditor();

        }

        public function canAllocateIp(): bool {

            return false;

        }

    }

?>
