<?php

    namespace GameDash\Sdk\Module\Implementation\Service\MountAndBladeWarband\Resources\FileSystem;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class FileSystem extends Implementation\Service\FileSystem\FileSystem {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function isEnabled(): bool {

            return true;

        }

    }

?>
