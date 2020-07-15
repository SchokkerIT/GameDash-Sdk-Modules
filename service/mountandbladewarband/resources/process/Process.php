<?php

    namespace GameDash\Sdk\Module\Implementation\Service\MountAndBladeWarband\Resources\Process;

    use function \_\find;
    use \Electrum\Userland\Infrastructure\Node\Process\ProcessNotFoundException;
    use \Electrum\Utilities;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Infrastructure\Node\FileSystem;

    class Process extends Implementation\Service\Process\Process {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function start(): void {

            $Directory = $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile();

            if( $this->Instance->getSettings()->exists('wse.isEnabled') && $this->Instance->getSettings()->get('wse.isEnabled')->getValue() === true ) {

                $execPath = Utilities\FileSystem\Path::join(

                    $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile()->getPath()->toString(),
                    'WSELoaderServer.exe'

                );

                $command = 'cd ' . $Directory->getPath()->toString() . '/; screen -L -dmS ' . $this->Instance->getShortId() . ' wine ' . $execPath . ' -p mb_warband_dedicated.exe -r ' . $this->Instance->getSettings()->get('config')->getValue()
                    . ' -m "' . $this->Instance->getSettings()->get('module')->getValue() . '"';

            }
            else {

                $execPath = Utilities\FileSystem\Path::join(

                    $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile()->getPath()->toString(),
                    'mb_warband_dedicated.exe'

                );

                $command = 'cd ' . $Directory->getPath()->toString() . '/; screen -L -dmS ' . $this->Instance->getShortId() . ' wineconsole --backend=curses ' . $execPath . ' -r ' . $this->Instance->getSettings()->get('config')->getValue() . ' -m ' .
                    $this->Instance->getSettings()->get('module')->getValue()->get();

            }

            $SshConnection = $this->Node->getSsh()->createConnection();

            $SshConnection->exec( $command );

            $this->Instance->getProcess()->setId( $this->getScreenPid() );

        }

        public function stop(): void {

            try {

                $screenPid = $this->getScreenPid();

                if( $screenPid ) {

                    foreach($this->Node->getProcesses()->get($screenPid)->getChildren() as $Process) {

                        $Process->kill();

                    }

                    $this->Node->getProcesses()->get($screenPid)->kill();

                }

//                if( $this->Node->getProcesses()->exists( $this->Instance->getProcess()->getId() ) ) {
//
//                    $this->Node->getProcesses()->get($this->Instance->getProcess()->getId())->kill();
//
//                }

            }
            catch( \Exception $e ) {}

        }

        public function restart(): void {}

        public function isOnline(): bool {

            return $this->screenExists();

        }

        private function getScreenPid(): int {

            $SshConnection = $this->Node->getSsh()->createConnection();

            return (int)$SshConnection->exec('screen -list | grep ' . $this->Instance->getShortId() . ' | cut -f1 -d\'.\' | sed \'s/\W//g\'');

        }

        private function screenExists(): bool {

            $ScreenRootDirectory = $this->Node->getFileSystem()->getFiles()->get( new FileSystem\Path\Path( $this->Node, '/run/screen/S-root' ) );

            if( !$ScreenRootDirectory->exists() ) {

                return false;

            }

            $files = $ScreenRootDirectory->getDirectoryContents();

            return find($files, function( $File ) {

                return explode( '.', $File->getName())[1] === $this->Instance->getShortId();

            }) !== null;

        }

    }

?>
