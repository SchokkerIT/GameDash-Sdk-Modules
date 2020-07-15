<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Backup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Backup\Record;
    use \GameDash\Sdk\FFI\Instance\Backup\Storage;

    class Backup extends Implementation\Service\Backup\Backup {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );
            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function create( Storage\Storage $Storage ): void {

            $Storage->store( $this->Instance->getBackup()->getFileGroup() );

        }

        public function restore( Record\Record $Record ): void {

            $this->Instance->getFileSystem()->getRootDirectory()->deleteContents();

            $File = $this->Node->getFileSystem()->getFiles()->get(

                new Node\FileSystem\Path\Path( $this->Node, $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile()->toString() . '/backup.zip' )

            );

            $Record->getStorage()->saveTo( $File );

            try {

                $File->unzip(

                    $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile()

                );

                $this->Instance->getNetwork()->getPorts()->allocate();

                if( $this->Node->getUsers()->hasCurrent() ) {

                    $this->Node->getUsers()->getCurrent()->getPermissions()->assign();

                }

            }
            finally {

                $File->delete();

            }

        }

        public function getFileGroup(): Node\FileSystem\File\Group\Group {

            $Group = new Node\FileSystem\File\Group\Group( $this->Node );

            $RootDirectory = $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile();

            $Group->addFiles([

                new Node\FileSystem\File\Group\GroupFile(

                    $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/server.properties' ) ),
                    [ 'name' => '/server.properties' ]

                ),
                new Node\FileSystem\File\Group\GroupFile(

                    $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/eula.txt' ) ),
                    [ 'name' => '/eula.txt' ]

                ),
                new Node\FileSystem\File\Group\GroupFile(

                    $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/plugins' ) ),
                    [ 'name' => '/plugins', 'isDirectory' => true ]

                ),
                new Node\FileSystem\File\Group\GroupFile(

                    $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/world' ) ),
                    [ 'name' => '/world', 'isDirectory' => true ]

                ),
                new Node\FileSystem\File\Group\GroupFile(

                    $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/world_nether' ) ),
                    [ 'name' => '/world_nether', 'isDirectory' => true ]

                ),
                new Node\FileSystem\File\Group\GroupFile(

                    $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/world_the_end' ) ),
                    [ 'name' => '/world_the_end', 'isDirectory' => true ]

                ),
                new Node\FileSystem\File\Group\GroupFile(

                    $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/banned-players.json' ) ),
                    [ 'name' => '/banned_players.json' ]

                ),
                new Node\FileSystem\File\Group\GroupFile(

                    $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/usercache.json' ) ),
                    [ 'name' => '/usercache.json' ]

                ),
                new Node\FileSystem\File\Group\GroupFile(

                    $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/whitelist.json' ) ),
                    [ 'name' => '/whitelist.json' ]

                )

            ]);

            foreach( $RootDirectory->getDirectoryContents() as $File ) {

                if( $File->isDirectory() || $File->getExtension() !== 'jar' ) {

                    continue;

                }

                $Group->addFile(

                    new Node\FileSystem\File\Group\GroupFile(

                        $File

                    )

                );

            }

            return $Group;

        }

        public function getFileExtension(): string {

            return 'zip';

        }

        public function getFileContentType(): string {

            return 'application/x-zip-compressed';

        }

    }

?>
