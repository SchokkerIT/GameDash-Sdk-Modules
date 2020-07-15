<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\CounterStrikeGlobalOffensive\Resources\Action;

    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class CommonConfigurationItems {

        public static function createMaxConnectedClients(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('maxConnectedClients', 'slots');

            $Options->create('8 slots', 8)
                ->setPrice( new Price( 4 ) );

            $Options->create('12 slots', 12)
                ->setPrice( new Price( 6 ) );

            $Options->create('16 slots', 16)
                ->setPrice( new Price( 8 ) );

            $Options->create('20 slots', 20)
                ->setPrice( new Price( 10 ) );

            $Options->create('24 slots', 24)
                ->setPrice( new Price( 12 ) );

            $Options->create('28 slots', 28)
                ->setPrice( new Price( 14 ) );

            $Options->create('32 slots', 32)
                ->setPrice( new Price( 16 ) );

            return $Options;

        }

    }

?>
