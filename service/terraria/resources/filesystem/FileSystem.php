<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Terraria\Resources\FileSystem;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class FileSystem extends Implementation\Service\FileSystem\FileSystem {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var FFI\Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = new FFI\Instance\Instance( $this->Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function isEnabled(): bool {

            return true;

        }

    }

?>
