<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\FileSystem\ConfigEditor;

    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance\FileSystem\ConfigEditor\File\Definition;
    use \GameDash\Sdk\FFI\Instance\FileSystem\ConfigEditor\File\Setting;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        public function getFileDefinitions(): array {

            return [

                $this->getPropertyFileDefinition(),
                $this->getEulaFileDefinition()

            ];

        }

        private function getEulaFileDefinition(): Definition\Definition {

            $Definition = new Definition\Definition([ 'eula.txt' ]);

                $Definition->setEol("\n");
                $Definition->setSeparators([

                    '=',
                    '{whitespace}={whitespace}'

                ]);
                $Definition->setIgnoredStrings(['#']);

            return $Definition;

        }

        private function getPropertyFileDefinition(): Definition\Definition {

            $Definition = new Definition\Definition([ '*.properties' ]);

                $Definition->setEol("\n");
	            $Definition->setSeparators([

                    '=',
                    '{whitespace}={whitespace}'

                ]);
	            $Definition->setIgnoredStrings(['#']);
                $Definition->setSettingFormats([

                    new Setting\Formatting\Formatting('broadcast-rcon-to-ops', 'Broadcast RCON to ops', [

                        'options' => [

                            new Setting\Formatting\Option('Yes', 'true'),
                            new Setting\Formatting\Option('No', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('broadcast-console-to-ops', 'Broadcast console to ops', [

                        'options' => [

                            new Setting\Formatting\Option('Yes', 'true'),
                            new Setting\Formatting\Option('No', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('view-distance', 'View distance'),
                    new Setting\Formatting\Formatting('max-build-height', 'Max build height'),
                    new Setting\Formatting\Formatting('server-ip', 'Server IP address'),
                    new Setting\Formatting\Formatting('level-seed', 'Level seed'),
                    new Setting\Formatting\Formatting('allow-nether', 'Allow nether', [

                        'options' => [

                            new Setting\Formatting\Option('Yes', 'true'),
                            new Setting\Formatting\Option('No', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('enable-command-block', 'Enable command block', [

                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('gamemode', 'Game mode'),
                    new Setting\Formatting\Formatting('op-permission-level', 'OP permission level', [

                        'options' => [

                            new Setting\Formatting\Option('Bypass spawn protection', '1'),
                            new Setting\Formatting\Option('Use singleplayer cheat commands and use commandblocks', '2'),
                            new Setting\Formatting\Option('Use multiplayer cheat commands and manage players (/ban, /op etc)', '3'),
                            new Setting\Formatting\Option('Use all commands including /stop, /save-all, /save-on etc', '4')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('prevent-proxy-connections', 'Prevent proxy connections', [

                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('generator-settings', 'Generator settings', [

                        'description' => 'Used to customize world generation'

                    ]),
                    new Setting\Formatting\Formatting('resource-pack', 'Resource pack', [

                        'description' => 'URI/URL to a resource pack. Escaping of : and = characters with a backslash is required'

                    ]),
                    new Setting\Formatting\Formatting('resource-pack-sha1', 'Resource pack SHA-1', [

                        'description' => 'SHA-1 digest of the resource pack. Used for checking the validity of cache'

                    ]),
                    new Setting\Formatting\Formatting('level-name', 'Level name'),
                    new Setting\Formatting\Formatting('player-idle-timeout', 'Player idle timeout'),
                    new Setting\Formatting\Formatting('rcon.password', 'RCON password'),
                    new Setting\Formatting\Formatting('motd', 'MOTD'),
                    new Setting\Formatting\Formatting('force-gamemode', 'Force gamemode', [

                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('pvp', 'PVP', [

                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('spawn-npcs', 'Spawn NPCs', [

                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('spawn-animals', 'Spawn animals', [

                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('generate-structures', 'Generate structures', [

                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('difficulty', 'Difficulty', [

                        'options' => [

                            new Setting\Formatting\Option('Peaceful', '1'),
                            new Setting\Formatting\Option('Easy', '1'),
                            new Setting\Formatting\Option('Normal', '2'),
                            new Setting\Formatting\Option('Hard', '3')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('function-permission-level', 'Function permission level'),
                    new Setting\Formatting\Formatting('level-type', 'Level type', [

                        'options' => [

                            new Setting\Formatting\Option('Default', 'default'),
                            new Setting\Formatting\Option('Flat', 'flat'),
                            new Setting\Formatting\Option('Largebiomes', 'largebiomes'),
                            new Setting\Formatting\Option('Amplified', 'amplified'),
                            new Setting\Formatting\Option('Buffet', 'buffet')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('level-name', 'Level name'),
                    new Setting\Formatting\Formatting('spawn-monsters', 'Spawn monsters', [

                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('max-players', 'Max players'),
                    new Setting\Formatting\Formatting('online-mode', 'Online mode', [

                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('allow-flights', 'Allow flights', [

                        'options' => [

                            new Setting\Formatting\Option('Yes', 'true'),
                            new Setting\Formatting\Option('No', 'false')

                        ]

                    ]),
                    new Setting\Formatting\Formatting('max-world-size', 'Max world size'),
                    new Setting\Formatting\Formatting('snooper-enabled', 'Enable snooping', [

                        'description' => 'Enable snooping statistics to Mojang',
                        'options' => [

                            new Setting\Formatting\Option('Enabled', 'true'),
                            new Setting\Formatting\Option('Disabled', 'false')

                        ]

                    ])

                ]);

	        return $Definition;

        }

    }

?>
