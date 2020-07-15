<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Terraria\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\Terminal;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotRunningException;

    class Process extends Implementation\Service\Process\Process {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Process\Process */
        private $Process;

        /** @var Instance\Infrastructure\Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Process = $this->Instance->getProcess();
            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function start(): void {

            $ChildProcess = $this->Node->getProcesses()->getChildProcesses()->create();

            $ChildProcess->setArgs();

            if( $this->Node->getOperatingSystems()->getCurrent()->isLinux() ) {

                $ChildProcess->setTerminal( new Terminal\Pty() );

                $ChildProcess->setArgs([

                    'TerrariaServer.exe',
                    '-ip',
                    $this->Instance->getNetwork()->getIps()->getCurrent()->toString(),
                    '-port',
                    $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber(),
                    '-maxplayers',
                    $this->Instance->getSettings()->get('maxConnectedClients')->getValue(),
                    '-world',
                    $this->Instance->getSettings()->get('world')->getValue(),
                    '-autocreate',
                    '1'

                ]);

                $ChildProcess->setExecutable('mono');

            }
            else if( $this->Node->getOperatingSystems()->getCurrent()->isWindows() ) {

                $ChildProcess->setExecutable('TerrariaServer.exe');

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
