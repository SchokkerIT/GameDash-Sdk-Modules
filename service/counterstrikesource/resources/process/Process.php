<?php

    namespace GameDash\Sdk\Module\Implementation\Service\CounterStrikeSource\Resources\Process;

    use \Electrum;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\Terminal;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotRunningException;

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

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->createDefault();

            $ChildProcess->setTerminal( new Terminal\Pty() );

            $ChildProcess->setArgs([

                '-game',
                'cstrike',
                '-console',
                '-ip',
                $this->Instance->getNetwork()->getIps()->getCurrent()->toString(),
                '-port',
                $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber(),
                '+map',
                $this->Instance->getSettings()->get('map')->getValue(),
                '+maxplayers',
                $this->Instance->getSettings()->get('maxConnectedClients')->getValue()

            ]);

            if( $this->Node->getOperatingSystems()->getCurrent()->isLinux() ) {

                $ChildProcess->setExecutable('./scrds_run');

            }

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
