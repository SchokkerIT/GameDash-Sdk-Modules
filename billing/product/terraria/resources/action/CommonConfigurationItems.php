<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\Terraria\Resources\Action;

    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class CommonConfigurationItems {

        public static function createMaxConnectedClients(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('maxConnectedClients', 'slots');

            $Options->create('10 slots', 10)
                ->setPrice( new Price( 5 ) );

            $Options->create('12 slots', 12)
                ->setPrice( new Price( 6 ) );

            $Options->create('14 slots', 14)
                ->setPrice( new Price( 7 ) );

            $Options->create('16 slots', 16)
                ->setPrice( new Price( 8 ) );

            $Options->create('18 slots', 18)
                ->setPrice( new Price( 9 ) );

            $Options->create('20 slots', 20)
                ->setPrice( new Price( 10 ) );

            $Options->create('22 slots', 22)
                ->setPrice( new Price( 11 ) );

            $Options->create('24 slots', 24)
                ->setPrice( new Price( 12 ) );

            $Options->create('26 slots', 26)
                ->setPrice( new Price( 13 ) );

            $Options->create('28 slots', 28)
                ->setPrice( new Price( 14 ) );

            $Options->create('30 slots', 30)
                ->setPrice( new Price( 15 ) );

            $Options->create('32 slots', 32)
                ->setPrice( new Price( 16 ) );

            return $Options;

        }

    }

?>
