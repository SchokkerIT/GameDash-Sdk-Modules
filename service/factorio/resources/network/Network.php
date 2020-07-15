<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Factorio\Resources\Network;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Ip\Ip;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\ConfigEditor;

    class Network extends Implementation\Service\Network\Network {

        public function canAllocateIp(): bool {

            return false;

        }

    }

?>
