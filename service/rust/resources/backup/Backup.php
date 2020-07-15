<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Rust\Resources\Backup;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\File\File;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path\Path;
    use \GameDash\Sdk\FFI\Instance\Backup\Record\Record;
    use \GameDash\Sdk\FFI\Instance\Backup\Storage\Storage;

    class Backup extends Implementation\Service\Backup\Backup {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function create( Storage $Storage ): void {

            $Group = $this->getFileGroup()->toAbsolute();

            $Group->zip(

                $Storage->getFile()

            );

        }

        public function restore( Record $Record ): void {

            $this->Instance->getFileSystem()->getRootDirectory()->deleteContents();

            $Record->getStorage()->getFile()->unzip(

                $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile()

            );

        }

        public function getFileGroup(): Instance\FileSystem\File\Group {

            $Group = new Instance\FileSystem\File\Group\Group( $this->Instance );

            $Group->addFile(

                $this->Instance->getFileSystem()->getFiles()->get(

                    new Path( $this->Instance, '/' )

                )

            );

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
