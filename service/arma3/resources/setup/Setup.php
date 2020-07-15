<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Arma3\Resources\Setup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;

    class Setup extends Implementation\Service\Setup\Setup {

        public function __construct( Gateway\Gateway $Gateway ) {



        }

        public function install( array $parameters ): void {



        }

        public function uninstall(): void {}

        /** @return Instance\Setup\Install\Parameter\Parameter[] */
        public function getInstallParameters(): array {

            return [];

        }

        /** @return Instance\Setup\Install\Parameter\Parameter[] */
        public function getInstallArguments(): array {

            return [];

        }

    }

?>
