<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Onset\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Os;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotFoundException;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotRunningException;

    class Process extends Implementation\Service\Process\Process {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Process\Process */
        private $Process;

        /** @var Instance\Infrastructure\Node\Node */
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

            $ChildProcess->setArgs([

                '--config',
                'server_config.json'

            ]);

            if( $this->OperatingSystem->isLinux() ) {

                $EnvironmentVariables = $this->Node->getSystem()->getEnvironment()->getVariables();

                $ChildProcess->addEnvironmentVariable(

                    'LD_LIBRARY_PATH', $EnvironmentVariables->exists('LD_LIBRARY_PATH') ? $EnvironmentVariables->get('LD_LIBRARY_PATH')->getValue() : null

                );

                $ChildProcess->setExecutable('./OnsetServer');

            }
            else if( $this->OperatingSystem->isWindows() ) {

                $ChildProcess->setExecutable('OnsetServer.exe');

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
