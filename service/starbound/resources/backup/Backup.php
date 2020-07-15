<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Starbound\Resources\Backup;

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

            }
            finally {

                $File->delete();

            }

        }

        public function getFileGroup(): Node\FileSystem\File\Group\Group {

            $RootDirectory = $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile();

            $Group = new Node\FileSystem\File\Group\Group( $this->Node );

                $Group->addFiles([

                    new Node\FileSystem\File\Group\GroupFile(

                        $this->Node->getFileSystem()->getFiles()->get( new Node\FileSystem\Path\Path( $this->Node, $RootDirectory->getPath()->toString() . '/' ) ),
                        [ 'name' => '/' ]

                    )

                ]);

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
