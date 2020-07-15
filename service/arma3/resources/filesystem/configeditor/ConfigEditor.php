<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Arma3\Resources\FileSystem\ConfigEditor;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                ''

            ];

        }

        public function getEol(): string {

            return "\r\n";

        }

        public function getSeparators(): array {

            return [

                '{whitespace}'

            ];

        }

        public function getIgnoredStrings(): array {

            return [ '//' ];

        }

        public function getFormatting(): array {

            return [



            ];

        }

    }

?>
