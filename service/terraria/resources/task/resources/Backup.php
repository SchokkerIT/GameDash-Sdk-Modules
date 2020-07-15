<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Terraria\Resources\Task\Resources;

    use \Electrum\Userland;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \Electrum\Userland\Instance\Task\Parameter\Parameters;

    class Backup extends Implementation\Service\Task\Resource {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var FFI\Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = new FFI\Instance\Instance(

                $this->Gateway->getParameters()->get('instance.id')->getValue()

            );

        }

        public function getTitle(): string {

            return 'Backup';

        }

        public function getDescription(): string {

            return 'Create a backup at the specified time(s). Be aware that creating a backup may delete older ones.';

        }

        public function execute( array $parameters ): void {

            if( $this->Instance->getBackup()->hasFiles() ) {

                $this->Instance->getBackup()->create();

            }

        }

        public function getExecutionCooldown(): int {

            return 86400;

        }

    }

?>
