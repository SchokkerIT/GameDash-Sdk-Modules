<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Factorio\Resources\FileSystem\ConfigEditor;

    use \Electrum\Json\Json;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class ConfigEditor
        extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor
        implements Implementation\Service\FileSystem\ConfigEditor\IStandalone

    {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getPaths(): array {

            return [

                '/data/*.json'

            ];

        }

        public function getEol(): string {

            return "\r";

        }

        public function getSeparators(): array {

            return [];

        }

        public function getIgnoredStrings(): array {

            return [];

        }

        public function getFormatting(): array {

            return [];

        }

        public function populateSettings( string $contents, callable $CreateSetting ): void {

            foreach( Json::decode( $contents ) as $name => $value ) {

                $CreateSetting(

                    $name,

                    is_array( $value ) ? Json::encode( $value ) : $value,

                    [

                        'isIgnored' => substr( $name, 0, 8 ) === '_comment'

                    ]

                );

            }

        }

        public function fromSettings( array $settings ): string {

            $formattedSettingsArray = [];

            foreach( $settings as $index => $setting ) {

                $name = $settings[ $index ]['name'];
                $value = $settings[ $index ]['value'];

                if( !empty( $setting['value'] ) && Json::isValid( $setting['value'] ) ) {

                    $proposedValue = Json::decode( $value );

                    if( is_array( $proposedValue ) ) {

                        $value = $proposedValue;

                    }

                }

                $formattedSettingsArray[ $name ] = $value;

            }

            return Json::encode( $formattedSettingsArray );

        }

    }

?>
