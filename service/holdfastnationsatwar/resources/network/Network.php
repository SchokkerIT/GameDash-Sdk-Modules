<?php

    namespace GameDash\Sdk\Module\Implementation\Service\HoldfastNationsAtWar\Resources\Network;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class Network extends Implementation\Service\Network\Network {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function canAllocateIp(): bool {

            return false;

        }

    }

?>
