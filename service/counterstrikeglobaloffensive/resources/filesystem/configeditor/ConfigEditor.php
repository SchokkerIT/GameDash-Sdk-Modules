<?php

    namespace GameDash\Sdk\Module\Implementation\Service\CounterStrikeGlobalOffensive\Resources\FileSystem\ConfigEditor;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                'csgo/cfg/server.cfg'

            ];

        }

        public function getEol(): string {

            return "\r";

        }

        public function getSeparators(): array {

            return [

                '{whitespace}'

            ];

        }

        public function getIgnoredStrings(): array {

            return [ '//', 'exec', 'log' ];

        }

        public function getFormatting(): array {

            return [

                'sv_password' => [

                    'description' => 'Server password'

                ],

                'hostname' => [

                    'description' => 'Server name'

                ]

            ];

        }

    }

?>
