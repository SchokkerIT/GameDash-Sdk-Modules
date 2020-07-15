<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Unturned\Resources\Console;

    use function \_\map;
    use \Electrum\Regex;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess;
    use \GameDash\Sdk\FFI\Instance;

    class Console extends Implementation\Service\Console\Console {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function sendInput( string $input ): ?string {

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->getCurrent();

            $ChildProcess->getIo()->sendInput( $input );

            return null;

        }

        public function getOutput( ?int $tail ): array {

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->getCurrent();

            return map($ChildProcess->getIo()->getOutput( $tail ), static function( ChildProcess\Io\Output\Item $Item ): Instance\Console\Io\Output\Item {

                return new Instance\Console\Io\Output\Item( $Item->getValue(), $Item->isError(), $Item->getTimeCreated() );

            });

        }

        public function isAvailable(): bool {

            return true;

        }

    }

?>
