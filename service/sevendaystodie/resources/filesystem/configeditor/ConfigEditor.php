<?php

    namespace GameDash\Sdk\Module\Implementation\Service\SevenDaysToDie\Resources\FileSystem\ConfigEditor;

    use function _\map;
    use \Sabre;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;

    class ConfigEditor
        extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor
        implements Implementation\Service\FileSystem\ConfigEditor\IStandalone

    {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                '*.xml'

            ];

        }

        public function getEol(): string {

            return "\r";

        }

        public function getIgnoredStrings(): array {

            return [ 'ServerPort', 'ServerVisibility', 'ServerDisabledNetworkProtocols', 'ServerMaxPlayerCount', 'ControlPanelPort', 'ServerAdminSlots', 'TerminalWindowEnabled' ];

        }

        public function populateSettings( string $contents, callable $CreateSetting ): void {

            $XmlService = new Sabre\Xml\Service();

            foreach( $XmlService->parse( $contents ) as $property ) {

                $name = $property['attributes']['name'];
                $value = $property['attributes']['value'];

                $CreateSetting( $name, $value );

            }

        }

        public function fromSettings( array $settings ): string {

            $XmlService = new Sabre\Xml\Service();

            return $XmlService->write(

                'ServerSettings', map($settings, static function( array $setting ) {

                    return [

                        'name' => 'property',
                        'attributes' => [

                            'name' => $setting['name'],
                            'value' => (string)$setting['value']

                        ]

                    ];

                })

            );

        }

        public function getFormatting(): array {

            return [

                'ServerName' => [

                    'title' => 'Server name'

                ],

                'ServerDescription' => [

                    'title' => 'Server description'

                ],

                'ServerWebsiteURL' => [

                    'title' => 'Server website URL'

                ],

                'ServerLoginConfirmationText' => [

                    'title' => 'Login confirmation text',
                    'description' => 'If set the user will see the message during joining the server and has to confirm it before continuing. For more complex changes to this window you can change the "serverjoinrulesdialog" window in XUi'

                ],

                'ServerMaxWorldTransferSpeedKiBs' => [

                    'title' => 'Server visibility',
                    'description' => 'Maximum (!) speed in kiB/s the world is transferred at to a client on first connect if it does not have the world yet. Maximum is about 1300 kiB/s, even if you set a higher value.'

                ],

                'ServerReservedSlots' => [

                    'title' => 'Reserved slots'

                ],

                'ServerReservedSlotsPermission' => [

                    'title' => 'Reserved slots permission level',
                    'description' => 'Permission level required to make use of reserved slots'

                ],

                'ControlPanelEnabled' => [

                    'title' => 'Control panel enabled',
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
                'ControlPanelPassword' => [

                    'title' => 'Control panel password'

                ]

            ];

        }

    }

?>
