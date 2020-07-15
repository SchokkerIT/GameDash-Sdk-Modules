<?php

    namespace GameDash\Sdk\Module\Implementation\Service\PostScriptum\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Os;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotFoundException;

    class Process extends Implementation\Service\Process\Process {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Process\Process */
        private $Process;

        /** @var Instance\Infrastructure\Node\Node */
        private $Node;

        /** @var Os\System */
        private $OperatingSystem;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = Instance\Instances::get( $this->Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Process = $this->Instance->getProcess();
            $this->Node = $this->Instance->getInfrastructure()->getNode();
            $this->OperatingSystem = $this->Node->getOperatingSystems()->getCurrent();

        }

        public function start(): void {

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->createDefault();

            $args = [

                'Port=' . $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber(),
                'QueryPort=' . $this->Instance->getNetwork()->getPorts()->getByName('query')->getNumber(),
                'RCONPORT=' . $this->Instance->getNetwork()->getPorts()->getByName('rcon')->getNumber(),
                'FIXEDMAXPLAYERS=' . $this->Instance->getSettings()->get('maxConnectedClients')->getValue(),
                '-fullcrashdump',
                '-log'

            ];

            if( $this->OperatingSystem->isWindows() ) {

                $ChildProcess->setExecutable('PostScriptum\\Binaries\\Win64\\PostScriptumServer.exe');
                $ChildProcess->setArgs($args);

            }
            else if( $this->OperatingSystem->isLinux() ) {

                $ldLibraryPath = $this->Node->getSystem()->getEnvironment()->getVariables()->exists('LD_LIBRARY_PATH') ? $this->Node->getSystem()->getEnvironment()->getVariables()->get('LD_LIBRARY_PATH')->getValue() : null;

                $Directory = $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile();

                $ChildProcess->addEnvironmentVariable(

                    'LD_LIBRARY_PATH', $Directory->getPath()->toString() . '/linux64/' . $ldLibraryPath

                );

                $ChildProcess->setExecutable('./PostScriptum/Binaries/Linux/PostScriptumServer');
                $ChildProcess->setArgs($args);

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
