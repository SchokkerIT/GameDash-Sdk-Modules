<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\Squad\Resources\Action;

    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class CommonConfigurationItems {

        public static function createMaxConnectedClientsItem(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('maxConnectedClients', 'slots');

            $Options->create('10 slots', 10)
                ->setPrice( new Price( 5 ) );

            $Options->create('20 slots', 20)
                ->setPrice( new Price( 10 ) );

            $Options->create('30 slots', 30)
                ->setPrice( new Price( 15 ) );

            $Options->create('40 slots', 40)
                ->setPrice( new Price( 20 ) );

            $Options->create('50 slots', 50)
                ->setPrice( new Price( 25 ) );

            $Options->create('60 slots', 60)
                ->setPrice( new Price( 27.5 ) );

            $Options->create('70 slots', 70)
                ->setPrice( new Price( 30 ) );

            $Options->create('80 slots', 80)
                ->setPrice( new Price( 32.5 ) );

            return $Options;

        }

    }

?>
