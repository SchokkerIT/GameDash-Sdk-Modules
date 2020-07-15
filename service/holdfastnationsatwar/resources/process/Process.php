<?php

    namespace GameDash\Sdk\Module\Implementation\Service\HoldfastNationsAtWar\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Instance\Process\Command;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotRunningException;

    class Process extends Implementation\Service\Process\Process {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Process\Process */
        private $Process;

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = Instance\Instances::get( $this->Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Process = $this->Instance->getProcess();
            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function start(): void {

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->createDefault();

            $StartCommand = $this->createStartCommand();

            $ChildProcess->unsetOperatingSystemUser();

            $ChildProcess->setExecutable( $StartCommand->getExecutable() );

            $ChildProcess->setArgs( $StartCommand->getArgs() );

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

        public function getCommands(): array {

            return [

                $this->createStartCommand()

            ];

        }

        private function createStartCommand(): Command\Command {

            $fps = $this->Instance->getSettings()->exists('fps') ? $this->Instance->getSettings()->get('fps')->getValue() : 60;

            $Directory = $this->Instance->getFileSystem()->getRootDirectory();

            $commonArgs = [

                '-startserver',
                '-batchmode',
                '-screen-width',
                '640',
                '-screen-height',
                '480',
                '-screen-quality',
                'Fastest',
                '-framerate',
                $fps,
                '-servercarbonplayers',
                '0',
                '-serverConfigFilePath',
                $Directory->getFile()->getPath()->createClone()->join( $this->Instance->getSettings()->get('config')->getValue() )->getAbsolute()->toString(),
                '-logFile',
                $Directory->getFile()->getPath()->createClone()->join('general.log')->getAbsolute()->toString(),
                '-logArchivesDirectory',
                $Directory->getFile()->getPath()->createClone()->join('logs_archive')->getAbsolute()->toString(),
                '-micSpammersPlayersFilePath',
                $Directory->getFile()->getPath()->createClone()->join('micspammers.txt')->getAbsolute()->toString(),
                '-mutedVoipPlayersFilePath',
                $Directory->getFile()->getPath()->createClone()->join('mutedvoipplayers.txt')->getAbsolute()->toString(),
                'mutedChatPlayersFilePath',
                $Directory->getFile()->getPath()->createClone()->join('mutedchatplayers.txt')->getAbsolute()->toString(),
                'bannedPlayersFilePath',
                $Directory->getFile()->getPath()->createClone()->join('bannedplayers.txt')->getAbsolute()->toString(),
                '-adminCommandsLogFilePath',
                $Directory->getFile()->getPath()->createClone()->join('adminscommands.log')->getAbsolute()->toString(),
                '-playersLogFilePath',
                $Directory->getFile()->getPath()->createClone()->join('players.log')->getAbsolute()->toString(),
                '-scoreboardLogFilePath',
                $Directory->getFile()->getPath()->createClone()->join('scoreboard.log')->getAbsolute()->toString(),
                '-chatLogFilePath',
                $Directory->getFile()->getPath()->createClone()->join('chat.log')->getAbsolute()->toString(),
                '-workshopDataPath',
                $Directory->getFile()->getPath()->createClone()->join('mods')->getAbsolute()->toString(),
                '-l',
                '"94.130.66.231"',
                '-o',
                '7101'

            ];

            /** @var Command\Command|null $Command */
            $Command = null;

            if( $this->Instance->getInfrastructure()->getNode()->getOperatingSystems()->getCurrent()->isLinux() ) {

                $args = [];

                array_push(

                    $args,
                    ...[

                        'wine',
                        '"' . $Directory->getFile()->getPath()->createClone()->join('Holdfast NaW.exe')->getAbsolute() . '"',

                    ],
                    ...$commonArgs

                );

                $Command = $this->Instance->getProcess()->getCommands()->create(

                    'start',
                    'xvfb-run',
                    $args

                );

            }
            else {

                $Command = $this->Instance->getProcess()->getCommands()->create(

                    'start',
                    '"' . $Directory->getFile()->getPath()->createClone()->join('Holdfast NaW.exe')->getAbsolute() . '"',
                    $commonArgs

                );

            }

            $Command->setTitle('Start command');

            return $Command;

        }

    }

?>
