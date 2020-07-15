<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\Terraria\Resources\Action;

    use function \_\find;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \Electrum\Userland\Sdk\Module\Common;
    use \GameDash\Sdk\FFI\Service;
    use \GameDash\Sdk\FFI\Infrastructure\Datacenter;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Billing\Subscription;
    use \GameDash\Sdk\FFI\Billing\Product;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;
    use \GameDash\Sdk\FFI\Billing\Price\Price;

    class CreateInstance extends Common\Billing\Product\Action\CreateInstance implements Implementation\Billing\Product\Action\IAction {

        public function getConfigurationItems(): array {

            return [

                $this->createNameConfigurationItem(),
                CommonConfigurationItems::createMaxConnectedClients(),
                $this->createLocationConfigurationItem()

            ];

        }

        public function manipulatePrice( Product\Product $Product, Configuration\Configuration $Configuration, Price $Price ): Price {

            return $Price;

        }

        public function canApplyDiscount(): bool {

            return true;

        }

        protected function getService(): Service\Service {

            return Service\Services::get('minecraft');

        }

        protected function getNode( Product\Product $Product, Configuration\Configuration $Configuration ): Node\Node {

            return $this->getService()->getInfrastructure()->getNodes()->getFinder()->find([

                'datacenters' => [ Datacenter\Datacenters::get( $Configuration->getItem('location')->getValue()->get() ) ]

            ]);

        }

        private function createNameConfigurationItem(): Configuration\Item\Variant\Text\Text {

            $Name = new Configuration\Item\Variant\Text\Text('name', 'Name');

            $Name->getValue()->getValidation()->setFunction(function( string $value ): Configuration\Item\Value\Validation\Result {

                if( strlen( $value ) > 32 ) {

                    return new Configuration\Item\Value\Validation\Result(false, 'Name must not be longer than 32 characters');

                }

                return new Configuration\Item\Value\Validation\Result(true);

            });

            return $Name;

        }

        private function createLocationConfigurationItem(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('location', 'Location');

                foreach( Datacenter\Datacenters::getAll() as $Datacenter ) {

                    if(

                        $Datacenter->isHidden()

                            ||

                        find($Datacenter->getNodes(), function( Node\Node $Node ): bool {

                            return $Node->getServices()->exists( $this->getService()->getId() );

                        }) === null

                    ) {

                        continue;

                    }

                    $Options->create(

                        $Datacenter->getLocation()->getCity() . ', ' . $Datacenter->getLocation()->getCountry()->getName(),
                        $Datacenter->getId()

                    );

                }

            return $Options;

        }

    }

?>
