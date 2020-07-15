<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Starbound\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
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

            if( $this->Node->getOperatingSystems()->getCurrent()->isLinux() ) {

                $WorkingDirectory = $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile()->getPath();

                $WorkingDirectory->join('linux');

                $ChildProcess->setWorkingDirectory(

                    $this->Node->getFileSystem()->getFiles()->get( $WorkingDirectory )

                );

                $ChildProcess->setExecutable('./starbound_server');

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
