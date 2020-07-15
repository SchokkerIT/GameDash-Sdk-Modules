<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Mordhau\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotRunningException;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Instance\Network;

    class Process extends Implementation\Service\Process\Process {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Process\Process */
        private $Process;

        /** @var Node\Node */
        private $Node;

        /** @var Network\Network */
        private $Network;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Process = $this->Instance->getProcess();
            $this->Node = $this->Instance->getInfrastructure()->getNode();
            $this->Network = $this->Instance->getNetwork();

        }

        public function start(): void {

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->createDefault();

            $ChildProcess->setExecutable('Mordhau/Binaries/Linux/MordhauServer-Linux-Shipping');

            $ChildProcess->setArgs([

                '-MultiHome=' . $this->Network->getIps()->getCurrent()->toString(),
                '-Port=' . $this->Network->getPorts()->getPrimary()->getNumber(),
                '-QueryPort=' . $this->Network->getPorts()->getByName('query')->getNumber(),
                '-BeaconPort=' . $this->Network->getPorts()->getByName('beacon')->getNumber()

            ]);

            $ChildProcess->spawn();

        }

        public function stop(): void {

            try {

                $ChildProcess = $this->Process->getChildProcesses()->getCurrent();

                $ChildProcess->stop();

            }
            catch( ChildProcessNotRunningException $e ) {}

        }

        public function restart(): void {}

        public function isOnline(): bool {

            return $this->Process->hasId() && $this->Node->getProcesses()->getChildProcesses()->get( $this->Process->getId() )->exists();

        }

    }

?>
