<?php

    namespace GameDash\Sdk\Module\Implementation\Service\CounterStrikeGlobalOffensive\Resources\Process;

    use \Electrum;
    use \Electrum\Json\Json;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Instance;
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

            $gameModeAndType = $this->getGameModeAndType();

            $ChildProcess->setArgs([

                '-game',
                'csgo',
                '-console',
                '-usercon',
                '+sv_setsteamaccount',
                $this->Instance->getSettings()->get('authKey')->getValue(),
                '-ip',
                $this->Instance->getNetwork()->getIps()->getCurrent()->toString(),
                '-port',
                $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber(),
                '+gametype',
                $gameModeAndType['gameType'],
                '+gamemode',
                $gameModeAndType['gameMode'],
                '-maxplayers_override',
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

        private function getGameModeAndType(): array {

            $map = Json::decode(

                ( new Electrum\FileSystem\File\File(

                    new Electrum\FileSystem\Path\Path( __DIR__ . '/../gamemodetypemap.json' ) )

                )->read()

            );

            return $map[ $this->Instance->getSettings()->get('gameMode')->getValue() ];

        }

    }

?>
