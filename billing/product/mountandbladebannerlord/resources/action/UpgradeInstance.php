<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\MountAndBladeBannerlord\Resources\Action;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \Electrum\Userland\Sdk\Module\Common;
    use \GameDash\Sdk\FFI\Service;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Billing\Product;
    use \GameDash\Sdk\FFI\Billing\Subscription;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class UpgradeInstance extends Common\Billing\Product\Action\UpgradeInstance implements Implementation\Billing\Product\Action\IAction {

        public function getConfigurationItems(): array {

            return [];

        }

        public function process( Product\Product $Product, Configuration\Configuration $Configuration, array $options ): void {}

        private function updateConfigFile( Instance\Instance $Instance, Configuration\Configuration $Configuration ): void {}

        protected function getService(): Service\Service {

            return Service\Services::get('mountandbladebannerlord');

        }

    }

?>
