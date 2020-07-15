<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\Mordhau\Resources\Action;

    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class CommonConfigurationItems {

        public static function createMaxConnectedClientsItem(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('maxConnectedClients', 'slots');

            $Options->create('8 slots', 8)
                ->setPrice( new Price( 3 ) );

            $Options->create('16 slots', 16)
                ->setPrice( new Price( 6 ) );

            $Options->create('32 slots', 32)
                ->setPrice( new Price( 12 ) );

            $Options->create('48 slots', 48)
                ->setPrice( new Price( 18 ) );

            $Options->create('64 slots', 64)
                ->setPrice( new Price( 24 ) );

            return $Options;

        }

    }

?>
