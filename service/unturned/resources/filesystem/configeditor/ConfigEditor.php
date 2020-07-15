<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Unturned\Resources\FileSystem\ConfigEditor;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                'Servers/primary/Server/commands.dat'

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

            return [ '#', '-', 'port', 'bind', 'maxplayers' ];

        }

        public function getFormatting(): array {

            return [

                'name' => [

                    'title' => 'Server name'

                ],
                'map' => [

                    'title' => 'Map name'

                ],
                'welcome' => [

                    'title' => 'Welcome message'

                ],
                'decay' => [

                    'title' => 'Player decay',
                    'description' => 'Assigns the amount of time a player, or their group members, can be offline before their structures can be removed by anyone. Default is 604800 (7 days)'

                ],
                'cycle' => [

                    'title' => 'Day/night cycle in seconds'

                ],
                'chatrate' => [

                    'title' => 'Chatrate',
                    'description' => 'Assigns the minimum amount of time between chat messages in order to prevent spam'

                ]

            ];

        }

    }

?>
