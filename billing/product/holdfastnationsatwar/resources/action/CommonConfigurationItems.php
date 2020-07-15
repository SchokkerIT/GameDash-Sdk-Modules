<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\HoldfastNationsAtWar\Resources\Action;

    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class CommonConfigurationItems {

        public static function createMaxConnectedClients(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('maxConnectedClients', 'slots');

            $Options->create('10 slots', 10)
                ->setPrice( new Price( 3 ) );

            $Options->create('20 slots', 20)
                ->setPrice( new Price( 4 ) );

            $Options->create('30 slots', 30)
                ->setPrice( new Price( 5.5 ) );

            $Options->create('40 slots', 40)
                ->setPrice( new Price( 7 ) );

            $Options->create('50 slots', 50)
                ->setPrice( new Price( 8.5 ) );

            $Options->create('60 slots', 60)
                ->setPrice( new Price( 10 ) );

            $Options->create('70 slots', 70)
                ->setPrice( new Price( 11.5 ) );

            $Options->create('80 slots', 80)
                ->setPrice( new Price( 13 ) );

            $Options->create('90 slots', 90)
                ->setPrice( new Price( 14.5 ) );

            $Options->create('100 slots', 100)
                ->setPrice( new Price( 16 ) );

            $Options->create('125 slots', 125)
                ->setPrice( new Price( 18.5 ) );

            $Options->create('150 slots', 150)
                ->setPrice( new Price( 21 ) );

            $Options->create('175 slots', 175)
                ->setPrice( new Price( 23.5 ) );

            $Options->create('200 slots', 200)
                ->setPrice( new Price( 26 ) );

            return $Options;

        }

    }

?>
