<?php

    namespace GameDash\Sdk\Module\Implementation\Service\MountAndBladeWarband\Resources\FileSystem\ConfigEditor;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                '/*.txt'

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

            return [ '#', 'add_map', 'add_factions', 'start', 'set_upload_limit', 'set_num_bots_voteable' ];

        }

        public function getFormatting(): array {

            return [

                'set_pass_admin' => [

                    'title' => 'Admin password'

                ],
                'set_server_name' => [

                    'title' => 'Server name'

                ],
                'set_welcome_message' => [

                    'title' => 'Welcome message'

                ],
                'set_mission' => [

                    'title' => 'Mission'

                ],
                'set_num_bots_voteable' => [

                    'title' => 'Voteable bot count'

                ],
                'set_map' => [

                    'title' => 'Map'

                ],
                'set_team_point_limit' => [

                    'title' => 'Team point limit'

                ],
                'set_randomize_factions' => [

                    'title' => 'Randomize factions',
                    'options' => [

                        [

                            'name' => 'Yes',
                            'value' => '1'

                        ],

                        [

                            'name' => 'No',
                            'value' => '0'

                        ]

                    ]

                ]

            ];

        }

    }

?>
