<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Onset\Resources\FileSystem\ConfigEditor;

    use function _\map;
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

                '/server_config.json'

            ];

        }

        public function getEol(): string {

            return "\r";

        }

        public function getIgnoredStrings(): array {

            return [ 'ipaddress', 'port' ];

        }

        public function populateSettings( string $contents, callable $CreateSetting ): void {

            foreach( Json::decode( $contents ) as $name => $value ) {

                $CreateSetting(

                    $name,
                    !is_string( $value ) ? Json::encode( $value ) : $value

                );

            }

        }

        public function fromSettings( array $settings ): string {

            $object = [];

            foreach( $settings as $setting ) {

                $value = $setting['value'];

                if( $value !== null && Json::isValid( $setting['value'] ) ) {

                    $value = Json::decode( $setting['value'] );

                }

                $object[ $setting['name'] ] = $value;

            }

            return Json::encode( $object );

        }

        public function getFormatting(): array {

            return [



            ];

        }

    }

?>
