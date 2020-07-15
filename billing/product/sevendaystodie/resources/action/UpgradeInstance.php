<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\SevenDaysToDie\Resources\Action;

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

        /** @var Gateway\Gateway */
        private $Gateway;

        public function __construct( Gateway\Gateway $Gateway ) {

            parent::__construct( $Gateway );

            $this->Gateway = $Gateway;

    }

        public function getConfigurationItems(): array {

            return [

                new Configuration\Item\Variant\Text\Text('instance_id', 'Instance id'),
                CommonConfigurationItems::createMaxConnectedClientsItem()

            ];

        }

        public function process( Product\Product $Product, Configuration\Configuration $Configuration, array $options ): void {

            $Instance = Instance\Instances::get( $Configuration->getItem('instance_id')->getValue()->get() );

            $Instance->getSettings()->get('maxConnectedClients')->setValue(

                $Configuration->getItem('maxConnectedClients')->getValue()->get()

            );

            $this->updateConfigFile( $Instance, $Configuration );

        }

        private function updateConfigFile( Instance\Instance $Instance, Configuration\Configuration $Configuration ): void {

            $File = $Instance->getFileSystem()->getConfigEditor()->getFiles()->get(

                new FileSystem\Path\Path( $Instance, $Instance->getSettings()->get('config')->getValue() )

            );

            $Settings = $File->getSettings();

            $Settings->getFirst('ServerMaxPlayerCount')->setValue(

                $Configuration->getItem('maxConnectedClients')->getValue()->get()

            );

            $Settings->commit();

        }

        protected function getService(): Service\Service {

            return Service\Services::get('7daystodie');

        }

    }

?>
