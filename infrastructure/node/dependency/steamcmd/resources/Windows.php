<?php

    namespace GameDash\Sdk\Module\Implementation\Infrastructure\Node\Dependency\SteamCmd\Resources;

    use \Electrum\Uri\Uri;
    use \Electrum\Utilities;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\FileSystem;
    use \GameDash\Sdk\FFI\Infrastructure\Node\FileSystem\Path;

    class Windows extends Implementation\Infrastructure\Node\Dependency\Dependency {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Node\Node */
        private $Node;

        /** @var FileSystem\FileSystem */
        private $FileSystem;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Node = new Node\Node( $this->Gateway->getParameters()->get('node.id')->getValue() );
            $this->FileSystem = $this->Node->getFileSystem();

        }

        public function install(): void {

            if( !$this->Node->getFileSystem()->getFiles()->getRegistered()->exists('steamcmd') ) {

                $this->Node->getFileSystem()->getFiles()->getRegistered()->create(

                    'steamcmd',
                    $this->FileSystem->getFiles()->get(

                        new Path\Path( $this->Node, $this->Node->getDaemon()->getFileSystem()->getDirectory()->getPath()->toString() . '\\steamcmd' )

                    ),
                    true

                );

            }

            $ZipFilePath = $this->getDirectoryPath();

            $ZipFilePath->join('windows.zip');

            $ZipFile = $this->FileSystem->getFiles()->get( $ZipFilePath );

            $ZipFile->downloadFrom(

                Uri::fromString( 'https://download.gamedash.io/infrastructure/node/dependency/steamcmd/windows.zip' )

            );

            $ZipFile->unzip(

                $this->FileSystem->getFiles()->get( $this->getDirectoryPath() )

            );

            $ZipFile->delete();

        }

        public function uninstall(): void {

            $this->FileSystem->getFiles()->getRegistered()->get('steamcmd')->getFile()->delete();

        }

        public function isAvailable(): bool {

            return $this->Node->getOperatingSystems()->getCurrent()->isWindows();

        }

        private function getDirectoryPath(): Path\Path {

            return $this->FileSystem->getFiles()->getRegistered()->get('steamcmd')->getFile()->getPath();

        }

    }

?>
