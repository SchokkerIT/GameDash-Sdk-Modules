<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\GarrysMod\Resources\Action;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \Electrum\Userland\Sdk\Module\Common;
    use \GameDash\Sdk\FFI\Service;
    use \GameDash\Sdk\FFI\Billing\Subscription;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class RenewInstance extends Common\Billing\Product\Action\RenewInstance implements Implementation\Billing\Product\Action\IAction {

        public function __construct( Gateway\Gateway $Gateway ) {

            parent::__construct( $Gateway );

        }

        public function getConfigurationItems(): array {

            return [

                new Configuration\Item\Variant\Text\Text('instance_id', 'Instance id')

            ];

        }

        protected function getService(): Service\Service {

            return Service\Services::get('garrysmod');

        }

    }

?>
