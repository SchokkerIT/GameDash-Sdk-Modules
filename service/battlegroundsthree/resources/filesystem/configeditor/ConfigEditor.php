<?php

    namespace GameDash\Sdk\Module\Implementation\Service\BattleGroundsThree\Resources\FileSystem\ConfigEditor;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                'bg3/cfg/server.cfg'

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

                'hostname' => [

                    'title' => 'Name'

                ],
                'mp_falldamage' => [

                    'title' => 'Fall damage'

                ],
                'mp_timelimit' => [

                    'title' => 'Time limit'

                ],
                'sv_logsdir' => [

                    'title' => 'Logs directory'

                ],
                'sv_ctf_capturestyle' => [

                    'title' => 'CTF capture style'

                ],
                'sv_ctf_flagalerts' => [

                    'title' => 'CTF flag alerts'

                ],
                'sv_ctf_returnstyle' => [

                    'title' => 'CTF return style'

                ]

            ];

        }

    }

?>
