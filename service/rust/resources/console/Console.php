<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Rust\Resources\Console;

    use function \_\map;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Instance;

    class Console extends Implementation\Service\Console\Console {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var FFI\Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = new FFI\Instance\Instance( $this->Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function sendInput( string $input ): ?string {

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->getCurrent();

            $ChildProcess->getIo()->sendInput( $input );

            return null;

        }

        public function getOutput( ?int $tail ): array {

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->getCurrent();

            return map($ChildProcess->getIo()->getOutput( $tail ), function( Node\Process\ChildProcess\Io\Output\Item $Item ): Instance\Console\Io\Output\Item {

                return new Instance\Console\Io\Output\Item( $Item->getValue(), $Item->isError(), $Item->getTimeCreated() );

            });

        }

        public function isAvailable(): bool {

            return true;

        }

    }

?>
