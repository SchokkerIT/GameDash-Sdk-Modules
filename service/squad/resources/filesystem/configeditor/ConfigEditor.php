<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Squad\Resources\FileSystem\ConfigEditor;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class ConfigEditor extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var FFI\Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = new FFI\Instance\Instance( $this->Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function getPaths(): array {

            return [

                'SquadGame/ServerConfig/Server.cfg'

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

                    'description' => 'Server name'

                ],

                'MaxPlayers' => [

                    'description' => 'Server name'

                ]

            ];

        }

    }

?>
