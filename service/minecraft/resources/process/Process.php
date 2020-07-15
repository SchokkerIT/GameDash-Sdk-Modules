<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Process;

    use function \_\filter;
    use function \_\find;
    use function \_\first;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Instance\Process\Command;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Dependency;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotFoundException;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotRunningException;

    class Process extends Implementation\Service\Process\Process {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Process\Process */
        private $Process;

        /** @var Instance\Infrastructure\Node\Node */
        private $Node;

        /** @var FileSystem\FileSystem */
        private $FileSystem;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Process = $this->Instance->getProcess();
            $this->Node = $this->Instance->getInfrastructure()->getNode();
            $this->FileSystem = $this->Instance->getFileSystem();

        }

        public function start(): void {

            if( !$this->eulaExists() || !$this->eulaIsApproved() ) {

                $this->generateEula();

            }

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->createDefault();

            $StartCommand = $this->createStartCommand();

            $ChildProcess->setExecutable( $StartCommand->getExecutable() );

            $ChildProcess->setArgs( $StartCommand->getArgs() );

            $ChildProcess->spawn();

        }

        public function stop(): void {

            $Console = $this->Instance->getConsole();

            $Console->getIo()->getInput()->send('say Server will shut down in 5 seconds');

            $Console->getIo()->getInput()->send('save-all');

//            sleep(5);

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

        public function getCommands(): array {

            return [

                $this->createStartCommand()

            ];

        }

        private function createStartCommand(): Command\Command {

            $JavaVersion = $this->getJavaVersion();

            $ramMB = $this->Instance->getSettings()->get('ram_mb')->getValue();

            $pathSeparator = $this->Node->getOperatingSystems()->getCurrent()->getFileSystem()->getSeparator();

            $Command = $this->Instance->getProcess()->getCommands()->create(

                'start',
                (

                    $JavaVersion && $JavaVersion->getSetup( $this->Node )->getInstallLocation() ?

                        $JavaVersion->getSetup( $this->Node )->getInstallLocation()->getPath()->toString() . $pathSeparator . 'bin' . $pathSeparator . 'java'

                            :

                        'java'

                ),
                [

                    '-Xmx' . $ramMB . 'M',
                    '-Xms' . $ramMB . 'M',
                    '-XX:+UseG1GC',
                    '-XX:+UnlockExperimentalVMOptions',
                    '-XX:MaxGCPauseMillis=100',
                    '-XX:+DisableExplicitGC',
                    '-XX:TargetSurvivorRatio=90',
                    '-XX:G1NewSizePercent=50',
                    '-XX:G1MaxNewSizePercent=80',
                    '-XX:G1MixedGCLiveThresholdPercent=35',
                    '-XX:+AlwaysPreTouch',
                    '-XX:+ParallelRefProcEnabled',
                    '-jar',
                    $this->Instance->getSettings()->get('jar')->getValue(),
                    'nogui'

                ]

            );

            $Command->setTitle('Start command');

            return $Command;

        }

        private function getJavaVersion(): ?Dependency\Dependency {

            if( $this->Instance->getSettings()->exists('java_version') ) {

                $name = $this->Instance->getSettings()->get('java_version')->getValue();

                if( $this->Node->getDependencies()->exists( $name ) ) {

                    return $this->Node->getDependencies()->get( $name );

                }

            }

            $JdkGroup = $this->Node->getDependencies()->getGroups()->get('jdk');

            if( !$JdkGroup ) {

                return null;

            }

            /** @var Dependency\Group\Item[] $items */
            $items = filter(

                $JdkGroup->getItems(),
                function( Dependency\Group\Item $Item ): bool {

                    return $Item->getDependency()->getSetup( $this->Node )->isInstalled();

                }

            );

            /** @var Dependency\Group\Item $Primary */
            $Primary = find($items, static function( Dependency\Group\Item $Item ): bool { return $Item->isPrimary(); });

            if( $Primary ) {

                return $Primary->getDependency();

            }

            return count( $items ) > 0 ? first( $items )->getDependency() : null;

        }

        private function generateEula(): void {

            if( !$this->eulaExists() ) {

                $this->FileSystem->getFiles()->get( new Path\Path($this->Instance, 'eula.txt') )->create();

            }

            $EulaFile = $this->FileSystem->getConfigEditor()->getFiles()->get(

                new Path\Path($this->Instance, 'eula.txt')

            );

            if( !$EulaFile->getSettings()->exists('eula') ) {

                $EulaFile->getSettings()->add( $EulaFile->getSettings()->createInstance('eula', 'true') );

            }
            else {

                $EulaFile->getSettings()->getFirst('eula')->setValue('true');

            }

            $EulaFile->getSettings()->commit();

        }

        private function eulaExists(): bool {

            return $this->FileSystem->getFiles()->get( new Path\Path($this->Instance, 'eula.txt') )->exists();

        }

        private function eulaIsApproved(): bool {

            $Eula = $this->FileSystem->getConfigEditor()->getFiles()->get(

                new Path\Path($this->Instance, 'eula.txt')

            );

            return $Eula->getSettings()->exists('eula') && $Eula->getSettings()->getFirst('eula')->getValue() === 'true';

        }

    }

?>
