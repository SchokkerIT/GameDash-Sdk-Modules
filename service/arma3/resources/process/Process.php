<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Arma3\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotFoundException;

    class Process extends Implementation\Service\Process\Process {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Process\Process */
        private $Process;

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Process = $this->Instance->getProcess();
            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function start(): void {



        }

        public function stop(): void {



        }

        public function restart(): void {}

        public function isOnline(): bool {

            if( !$this->Process->hasId() ) {

                return false;

            }

            try {

                return !$this->Node->getProcesses()->getChildProcesses()->get( $this->Process->getId() )->hasExited();

            }
            catch( ChildProcessNotFoundException $e ) {

                return false;

            }

        }

        public function usageIsMeasurable(): bool {

            return true;

        }

    }

?>
