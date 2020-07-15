<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\GarrysMod\Resources\Action;

    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class CommonConfigurationItems {

        public static function createMaxConnectedClients(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('maxConnectedClients', 'slots');

            $Options->create('10 slots', 10)
                ->setPrice( new Price( 3 ) );

            $Options->create('20 slots', 20)
                ->setPrice( new Price( 6 ) );

            $Options->create('30 slots', 30)
                ->setPrice( new Price( 9 ) );

            $Options->create('40 slots', 40)
                ->setPrice( new Price( 12 ) );

            $Options->create('50 slots', 50)
                ->setPrice( new Price( 15 ) );

            $Options->create('60 slots', 60)
                ->setPrice( new Price( 17 ) );

            $Options->create('70 slots', 70)
                ->setPrice( new Price( 19 ) );

            $Options->create('80 slots', 80)
                ->setPrice( new Price( 21 ) );

            $Options->create('90 slots', 90)
                ->setPrice( new Price( 23 ) );

            $Options->create('100 slots', 100)
                ->setPrice( new Price( 25 ) );

            return $Options;

        }

    }

?>
