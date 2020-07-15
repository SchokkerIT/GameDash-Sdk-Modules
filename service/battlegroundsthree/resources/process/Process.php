<?php

    namespace GameDash\Sdk\Module\Implementation\Service\BattleGroundsThree\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Os;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotFoundException;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotRunningException;

    class Process extends Implementation\Service\Process\Process {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Process\Process */
        private $Process;

        /** @var Node\Node */
        private $Node;

        /** @var Os\System */
        private $OperatingSystem;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Process = $this->Instance->getProcess();
            $this->Node = $this->Instance->getInfrastructure()->getNode();
            $this->OperatingSystem = $this->Node->getOperatingSystems()->getCurrent();

        }

        public function start(): void {

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->createDefault();

            $Directory = $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile();

            $ChildProcess->setWorkingDirectory(

                $this->Node->getFileSystem()->getFiles()->get(

                    new Node\FileSystem\Path\Path( $this->Node, $Directory->getPath()->toString() . $this->OperatingSystem->getFileSystem()->getSeparator() . 'server' )

                )

            );

            $ChildProcess->setExecutable('srcds.exe');
            $ChildProcess->setArgs([

                '-steam',
                '-console',
                '-game',
                '"..\bg3"',
                '+ip',
                $this->Instance->getNetwork()->getIps()->getCurrent()->toString(),
                '-port',
                $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber(),
                '+map',
                $this->Instance->getSettings()->get('map')->getValue(),
                '+maxplayers',
                $this->Instance->getSettings()->get('maxConnectedClients')->getValue(),
                '-autoupdate'

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
