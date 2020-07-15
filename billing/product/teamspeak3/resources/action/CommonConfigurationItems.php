<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\TeamSpeak3\Resources\Action;

    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class CommonConfigurationItems {

        public static function createMaxConnectedClientsItem(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('maxConnectedClients', 'slots');

            $Options->create('10 slots', 10)
                ->setPrice( new Price( 2 ) );

            $Options->create('15 slots', 15)
                ->setPrice( new Price( 3 ) );

            $Options->create('20 slots', 20)
                ->setPrice( new Price( 4 ) );

            $Options->create('25 slots', 25)
                ->setPrice( new Price( 5 ) );

            $Options->create('30 slots', 30)
                ->setPrice( new Price( 6 ) );

            $Options->create('35 slots', 35)
                ->setPrice( new Price( 7 ) );

            $Options->create('40 slots', 40)
                ->setPrice( new Price( 8 ) );

            $Options->create('45 slots', 45)
                ->setPrice( new Price( 9 ) );

            $Options->create('50 slots', 50)
                ->setPrice( new Price( 10 ) );

            $Options->create('75 slots', 75)
                ->setPrice( new Price( 15 ) );

            $Options->create('100 slots', 100)
                ->setPrice( new Price( 20 ) );

            $Options->create('150 slots', 150)
                ->setPrice( new Price( 30 ) );

            $Options->create('200 slots', 200)
                ->setPrice( new Price( 40 ) );

            $Options->create('250 slots', 250)
                ->setPrice( new Price( 50 ) );

            $Options->create('300 slots', 300)
                ->setPrice( new Price( 60 ) );

            $Options->create('350 slots', 350)
                ->setPrice( new Price( 70 ) );

            $Options->create('400 slots', 400)
                ->setPrice( new Price( 80 ) );

            $Options->create('450 slots', 450)
                ->setPrice( new Price( 90 ) );

            $Options->create('500 slots', 500)
                ->setPrice( new Price( 100 ) );

            return $Options;

        }

    }

?>
