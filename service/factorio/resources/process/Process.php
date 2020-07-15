<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Factorio\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Infrastructure\Node\User;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;
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

            if( !$this->saveFileExists() ) {

                $this->createSaveFile();

            }

            $this->ensureMaxPlayers();

            $ChildProcess = $this->Instance->getProcess()->getChildProcesses()->createDefault();

            $ChildProcess->setArgs([

                '--start-server',
                $this->Instance->getSettings()->get('save')->getValue(),
                '--server-settings',
                $this->Instance->getSettings()->get('config')->getValue(),
                '--port',
                $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber(),
                '--rcon-port',
                $this->Instance->getNetwork()->getPorts()->getByName('rcon')->getNumber(),
                '--rcon-password',
                $this->Instance->getSettings()->get('rconPassword')->getValue()

            ]);

            if( $this->Node->getOperatingSystems()->getCurrent()->isLinux() ) {

                $ChildProcess->setExecutable('./bin/x64/factorio');

            }

            $ChildProcess->start();

        }

        public function stop(): void {

            $this->Instance->getConsole()->getIo()->getInput()->send('/server-save');

            sleep( 10 );

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

        private function saveFileExists(): bool {

            return $this->Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $this->Instance, $this->Instance->getSettings()->get('save')->getValue() )

            )->exists();

        }

        private function createSaveFile(): void {

            $ChildProcess = $this->Node->getProcesses()->getChildProcesses()->create();

            $ChildProcess->setWorkingDirectory( $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile() );

            /** @var User\User $User */
            $User = $this->Instance->getInfrastructure()->getNode()->getUsers()->getCurrent();

            $ChildProcess->spawn('sudo', [

                '-i',
                '-u',
                $User->getId(),
                './bin/x64/factorio',
                '--create',
                $this->Instance->getSettings()->get('save')->getValue()

            ], ['await' => true]);

            if( $ChildProcess->getIo()->getOutput()[0]->isError() ) {

                throw new \Exception('Could not create save file (' . $ChildProcess->getIo()->getOutput()[0]->getValue() . ')');

            }

            $User->getPermissions()->assign();

        }

        private function ensureMaxPlayers(): void {

            $maxPlayers = $this->Instance->getSettings()->get('maxConnectedClients')->getValue();

            $Settings = $this->Instance->getFileSystem()->getConfigEditor()->getFiles()->get(

                new Path\Path( $this->Instance, $this->Instance->getSettings()->get('config')->getValue() )

            )->getSettings();

            if( !$Settings->exists('max_players') || (int)$Settings->getFirst('max_players')->getValue() > $maxPlayers ) {

                if( !$Settings->exists('max_players') ) {

                    $Settings->createInstance('max_players');

                }
                else {

                    $Settings->getFirst('max_players')->setValue( $maxPlayers );

                }

            }

            $Settings->commit();

        }

    }

?>
