<?php

    namespace GameDash\Sdk\Module\Implementation\Service\MinecraftBedrock\Resources\FileSystem\ConfigEditor;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                'server.properties'

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

            return [ '#' ];

        }

        public function getFormatting(): array {

            return [

                'gamemode' => [

                    'description' => 'Sets the game mode for new players',
                    'options' => [

                        [

                            'name' => 'Survival',
                            'value' => 'survival'

                        ],

                        [

                            'name' => 'Creative',
                            'value' => 'creative'

                        ],

                        [

                            'name' => 'Adventure',
                            'value' => 'adventure'

                        ]

                    ]

                ],

                'difficulty' => [

                    'description' => 'Sets the difficulty of the world',
                    'options' => [

                        [

                            'name' => 'Peaceful',
                            'value' => 'peaceful'

                        ],

                        [

                            'name' => 'Easy',
                            'value' => 'easy'

                        ],

                        [

                            'name' => 'Normal',
                            'value' => 'normal'

                        ],

                        [

                            'name' => 'Hard',
                            'value' => 'hard'

                        ]

                    ]

                ],

                'allow-cheats' => [

                    'description' => 'If true then cheat-like commands can be used',
                    'options' => [

                        [

                            'name' => 'True',
                            'value' => 'true'

                        ],

                        [

                            'name' => 'False',
                            'value' => 'false'

                        ]

                    ]

                ],

                'online-mode' => [

                    'description' => 'If true then all players must be authenticated to Xbox Live',
                    'options' => [

                        [

                            'name' => 'True',
                            'value' => 'true'

                        ],

                        [

                            'name' => 'False',
                            'value' => 'false'

                        ]

                    ]

                ],

                'whitelist' => [

                    'description' => 'If true then all players must be listed in the whitelist.json file in order to join',
                    'options' => [

                        [

                            'name' => 'True',
                            'value' => 'true'

                        ],

                        [

                            'name' => 'False',
                            'value' => 'false'

                        ]

                    ]

                ],

                'level-name' => [

                    'description' => 'World name'

                ]

            ];

        }

    }

?>
