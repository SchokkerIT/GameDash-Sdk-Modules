<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Settings\Plugins\DirectoryContents\Resources;

    use function \_\map;
    use \Electrum\FileSystem\File\FileNotFoundException;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;

    class DirectoryContents extends Implementation\Instance\Settings\Plugin {

        /** @var Instance\Instance */
        private $Instance;

        /** @var FileSystem\FileSystem */
        private $FileSystem;

        private $PluginParameters;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->FileSystem = $this->Instance->getFileSystem();
            $this->PluginParameters = $Gateway->getParameters()->get('PluginParameters')->getValue();

        }

        public function getData(): array {

            return [

                'options' => map($this->getDirectoryContents(), function( $File ) {

                    return [

                        'value' => $this->shouldUseFullFilePath() ? substr( $File->getPath()->toString(), 1 ) : $File->getName()

                    ];

                })

            ];

        }

        public function validateValue( $value ): bool {

            foreach( $this->getDirectoryContents() as $File ) {

                if( $this->shouldUseFullFilePath() ) {

                    if( $File->getPath()->toString() === $value ) {

                        return true;

                    }

                }
                else if( $File->getName() === $value ) {

                    return true;

                }

            }

            return false;

        }

        private function getDirectoryContents(): array {

            $files = [];

            foreach( $this->PluginParameters->get('directories')->getValue() as $directory ) {

                $Directory = $this->FileSystem->getFiles()->get(

                    new FileSystem\Path\Path( $this->Instance, $directory )

                );

                try {

                    $directoryContents = $Directory->getDirectoryContents();

                }
                catch( FileNotFoundException $e ) {

                    continue;

                }

                foreach( $directoryContents as $File ) {

                    if( $File->isDirectory() && !$this->shouldIncludeDirectories() ) {

                        continue;

                    }

                    if( $this->PluginParameters->exists('extensions') ) {

                        if(

                            $File->getExtension() === null

                                ||

                            !in_array( $File->getExtension(), $this->PluginParameters->get('extensions')->getValue(), true )

                        ) {

                            continue;

                        }

                    }

                    $files[] = $File;

                }

            }

            return $files;

        }

        private function shouldIncludeDirectories(): bool {

            return $this->PluginParameters->exists('includeDirectories') && $this->PluginParameters->get('includeDirectories')->getValue() === true;

        }

        private function shouldUseFullFilePath(): bool {

            return $this->PluginParameters->exists('useFullFilePath') && $this->PluginParameters->get('useFullFilePath')->getValue() === true;

        }

    }

?>
