<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Mordhau\Resources\FileSystem\ConfigEditor;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                '/Mordhau/Saved/Config/LinuxServer/Game.ini'

            ];

        }

        public function getEol(): string {

            return "\r";

        }

        public function getSeparators(): array {

            return [

                '=',
                '{whitespace}={whitespace}'

            ];

        }

        public function getIgnoredStrings(): array {

            return [ '#', '[', 'MapRotation' ];

        }

        public function getFormatting(): array {

            return [];

        }

    }

?>
