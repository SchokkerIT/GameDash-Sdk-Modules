<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\Minecraft\Resources\Action;

    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class CommonConfigurationItems {

        public static function createRamItem(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('ram_mb', 'RAM');

            $Options->create('0.5 GB', 512)
                ->setPrice( new Price( 1.25 ) );

            $Options->create('1 GB', 1024)
                ->setPrice( new Price( 2.5 ) );

            $Options->create('2 GB', 2048)
                ->setPrice( new Price( 5 ) );

            $Options->create('3 GB', 3072)
                ->setPrice( new Price( 7.5 ) );

            $Options->create('4 GB', 4096)
                ->setPrice( new Price( 10 ) );

            $Options->create('5 GB', 5120)
                ->setPrice( new Price( 12.5 ) );

            $Options->create('6 GB', 6144)
                ->setPrice( new Price( 15 ) );

            $Options->create('7 GB', 7168)
                ->setPrice( new Price( 17.5 ) );

            $Options->create('8 GB', 8192)
                ->setPrice( new Price( 20 ) );

            $Options->create('10 GB', 10240)
                ->setPrice( new Price( 24 ) );

            $Options->create('12 GB', 12288)
                ->setPrice( new Price( 28 ) );

            $Options->create('14 GB', 14336)
                ->setPrice( new Price( 32 ) );

            $Options->create('16 GB', 16384)
                ->setPrice( new Price( 40 ) );

            return $Options;

        }

    }

?>
