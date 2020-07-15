<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\MountAndBladeWarband\Resources\Action;

    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class CommonConfigurationItems {

        public static function createMaxConnectedClientsItem(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('maxConnectedClients', 'slots');

            $Options->create('10 slots', 10)
                ->setPrice( new Price( 1.5 ) );

            $Options->create('20 slots', 20)
                ->setPrice( new Price( 3 ) );

            $Options->create('30 slots', 30)
                ->setPrice( new Price( 4.5 ) );

            $Options->create('40 slots', 40)
                ->setPrice( new Price( 6 ) );

            $Options->create('50 slots', 50)
                ->setPrice( new Price( 7.5 ) );

            $Options->create('60 slots', 60)
                ->setPrice( new Price( 8.9 ) );

            $Options->create('70 slots', 70)
                ->setPrice( new Price( 10.2 ) );

            $Options->create('80 slots', 80)
                ->setPrice( new Price( 11.4 ) );

            $Options->create('90 slots', 90)
                ->setPrice( new Price( 12.5 ) );

            $Options->create('100 slots', 100)
                ->setPrice( new Price( 13.5 ) );

            $Options->create('110 slots', 110)
                ->setPrice( new Price( 14.5 ) );

            $Options->create('120 slots', 120)
                ->setPrice( new Price( 15 ) );

            $Options->create('130 slots', 130)
                ->setPrice( new Price( 16 ) );

            $Options->create('140 slots', 140)
                ->setPrice( new Price( 17 ) );

            $Options->create('150 slots', 150)
                ->setPrice( new Price( 18 ) );

            $Options->create('160 slots', 160)
                ->setPrice( new Price( 19 ) );

            $Options->create('170 slots', 170)
                ->setPrice( new Price( 19.5 ) );

            $Options->create('180 slots', 180)
                ->setPrice( new Price( 20 ) );

            $Options->create('190 slots', 190)
                ->setPrice( new Price( 20.5 ) );

            $Options->create('200 slots', 200)
                ->setPrice( new Price( 21 ) );

            return $Options;

        }

    }

?>
