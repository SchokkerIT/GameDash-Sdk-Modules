<?php

    namespace GameDash\Sdk\Module\Implementation\Service\PostScriptum\Resources\FileSystem\ConfigEditor;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                '/PostScriptum/ServerConfig/Server.cfg'

            ];

        }

        public function getEol(): string {

            return "\r";

        }

        public function getSeparators(): array {

            return [

                '='

            ];

        }

        public function getIgnoredStrings(): array {

            return [ '//' ];

        }

        public function getFormatting(): array {

            return [

                'ServerName' => [

                    'title' => 'Server name'

                ],
                'NumReservedSlots' => [

                    'title' => 'Reserved slots'

                ],
                'ShouldAdvertise' => [

                    'title' => 'Show on server list',
                    'options' => [

                        [

                            'name' => 'Yes',
                            'value' => 'true'

                        ],

                        [

                            'name' => 'No',
                            'value' => 'false'

                        ]

                    ]

                ],
                'AllowTeamChanges' => [

                    'title' => 'Allow team changes'

                ],
                'PreventTeamChangeIfUnbalanced' => [

                    'title' => 'Prevent team changes if unbalanced'

                ],
                'EnforceTeamBalance' => [

                    'title' => 'Enforce team balance',
                    'options' => [

                        [

                            'name' => 'Yes',
                            'value' => 'true'

                        ],

                        [

                            'name' => 'No',
                            'value' => 'false'

                        ]

                    ]

                ],
                'AllowCommunityAdminAccess' => [

                    'title' => 'Allow community admin access',
                    'description' => 'Allow community admins to join with the admin role'

                ]

            ];

        }

    }

?>
