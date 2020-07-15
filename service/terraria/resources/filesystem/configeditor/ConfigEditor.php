<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Terraria\Resources\FileSystem\ConfigEditor;

    use \Electrum\Json\Json;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class ConfigEditor
        extends Implementation\Service\FileSystem\ConfigEditor\ConfigEditor
        implements Implementation\Service\FileSystem\ConfigEditor\IStandalone

    {

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

                '/tshock/config.json'

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

                    is_array( $value ) ? Json::encode( $value ) : $value

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
