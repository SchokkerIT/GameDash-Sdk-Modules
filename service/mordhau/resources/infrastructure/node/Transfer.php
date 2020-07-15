<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Mordhau\Resources\Infrastructure\Node;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;

    class Transfer extends Implementation\Service\Infrastructure\Node\Transfer {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function beforeTransfer(): void {

            return;

            $Source = $this->Instance->getInstaller()->getSources()->get('SteamCmd');

            $Resource = $Source->getResources()->get('629800');

            $Resource->install(

                $Resource->getVersions()->getLatest(),
                [

                    'ignoreAlreadyInstalled' => true

                ]

            );

        }

        public function afterTransfer(): void {}

    }

?>
