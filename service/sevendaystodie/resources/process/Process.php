<?php

    namespace GameDash\Sdk\Module\Implementation\Service\SevenDaysToDie\Resources\Process;

    use \Electrum\Time\Time;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotRunningException;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Infrastructure\Node\User;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Os\ArchitectureEnum;

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

            $Directory = $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile();

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->createDefault([

                'asUser' => $this->Node->getOperatingSystems()->getCurrent()->isWindows() === false

            ]);

            $ChildProcess->setArgs([

                '-configfile=' . $this->Instance->getSettings()->get('config')->getValue(),
//                '-logfile="' . $Directory->getPath()->toString() . $this->Node->getOperatingSystems()->getCurrent()->getFileSystem()->getSeparator() . 'log.txt"',
                '-logfile="C:\GameDash\instance\36f4ec12-939b-45a0-9583-34339a6878c4\daad8ed9-c17a-4728-be45-7ee2def88b3c\log.txt"',
                '-quit',
                '-batchmode',
                '-nographics',
                '-dedicated'

            ]);

            $Architecture = $this->Node->getOperatingSystems()->getCurrent()->getArchitecture();

            if( $this->Node->getOperatingSystems()->getCurrent()->isLinux() ) {

                $EnvironmentVariables = $this->Node->getSystem()->getEnvironment()->getVariables();

                $ChildProcess->addEnvironmentVariable(

                    'LD_LIBRARY_PATH', $Directory->getPath()->toString() . ';' . ( $EnvironmentVariables->exists('LD_LIBRARY_PATH') ? $EnvironmentVariables->get('LD_LIBRARY_PATH')->getValue() : null )

                );

                $ChildProcess->setExecutable( $Architecture->compare( ArchitectureEnum::x64() ) ? '7DaysToDieServer.x86_64' : '7DaysToDieServer.x86' );

                $ChildProcess->spawn();

            }
            else if( $this->Node->getOperatingSystems()->getCurrent()->isWindows() ) {

                $ChildProcess->setExecutable('7DaysToDieServer.exe');

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
