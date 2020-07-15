<?php

    namespace GameDash\Sdk\Module\Implementation\Infrastructure\Node\Dependency\SteamCmd\Resources;

    use function \_\map;
    use \Electrum\Uri\Uri;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\FileSystem;
    use \GameDash\Sdk\FFI\Infrastructure\Node\FileSystem\File;
    use \GameDash\Sdk\FFI\Infrastructure\Node\FileSystem\Path;

    class Linux extends Implementation\Infrastructure\Node\Dependency\Dependency {

        /** @var Node\Node */
        private $Node;

        /** @var FileSystem\FileSystem */
        private $FileSystem;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Node = Node\Nodes::get( $Gateway->getParameters()->get('node.id')->getValue() );

            $this->FileSystem = $this->Node->getFileSystem();

        }

        public function install(): void {

            if( !$this->Node->getFileSystem()->getFiles()->getRegistered()->exists('steamcmd') ) {

                $this->Node->getFileSystem()->getFiles()->getRegistered()->create(

                    'steamcmd',
                    $this->FileSystem->getFiles()->get(

                        new Path\Path( $this->Node, $this->Node->getDaemon()->getFileSystem()->getDirectory()->getPath()->toString() . '/steamcmd' )

                    ),
                    true

                );

            }

            $this->download();

            $this->makeFilesExecutable();

        }

        public function uninstall(): void {

            $this->FileSystem->getFiles()->getRegistered()->get('steamcmd')->getFile()->deleteDirectory();

        }

        public function isAvailable(): bool {

            return $this->Node->getOperatingSystems()->getCurrent()->isLinux();

        }

        private function getDirectoryPath(): Path\Path {

            return $this->FileSystem->getFiles()->getRegistered()->get('steamcmd')->getFile()->getPath();

        }

        private function download(): void {

            $ZipFilePath = $this->getDirectoryPath()->createClone()->join('linux.zip');

            $ZipFile = $this->FileSystem->getFiles()->get( $ZipFilePath );

            $ZipFile->downloadFrom(

                Uri::fromString( 'https://download.gamedash.io/infrastructure/node/dependency/steamcmd/linux.zip' )

            );

            $ZipFile->unzip(

                $this->FileSystem->getFiles()->get( $this->getDirectoryPath() )

            );

            $ZipFile->delete();

        }

        private function makeFilesExecutable(): void {

            $Group = $this->Node->getFileSystem()->getFiles()->createGroup();

            $Group->addFiles(

                map(

                    $this->Node->getFileSystem()->getFiles()->get( $this->getDirectoryPath() )->getDirectoryContentsRecursively(),
                    static function( File\File $File ) use ( $Group ) {

                        return $Group->createFile( $File );

                    }

                )

            );

            $Group->makeExecutable();

        }

    }

?>
