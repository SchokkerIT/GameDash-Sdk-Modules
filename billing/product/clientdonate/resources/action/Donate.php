<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\ClientDonate\Resources\Action;

    use \Electrum\Database;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \Electrum\Userland\Sdk\Module\Common;
    use \GameDash\Sdk\FFI\Client;
    use \GameDash\Sdk\FFI\Client\Billing\Credit;
    use \GameDash\Sdk\FFI\Client\Billing\Donate\Donator;
    use \GameDash\Sdk\FFI\Billing\Currency;
    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Subscription;
    use \GameDash\Sdk\FFI\Billing\Product;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class Donate implements Implementation\Billing\Product\Action\IAction {

        private $Currency;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Currency = Client\Billing\Credit::getCurrency();

        }

        public function process( Product\Product $Product, Configuration\Configuration $Configuration, array $options ): void {

            $MySQLTransaction = Database\Driver\MySQL\MySQLDriver::getInstance()->getTransactions()->start();

            try {

                $Client = Client\Clients::get(

                    $Configuration->getItem('client_id')->getValue()->get()

                );

                $RecipientClient = Client\Clients::get(

                    $Configuration->getItem('recipient_client_id')->getValue()->get()

                );

                $amount = (float)$Configuration->getItem('amount')->getValue()->get();

                $RecipientClient->getBilling()->getCredit()->addBalance( $amount );

                $RecipientClient->getBilling()->getDonations()->create(

                    new Donator( $Client->getName() ), $amount

                );

                $Sender = $RecipientClient->getEmail()->createSender();

                    $Sender->setTitle( 'You have received a donation from ' . $Client->getEmail()->getAddress()->toString() );
                    $Sender->setMessage(

                        $Client->getEmail()->getAddress()->toString() . ' sent you a ' . Credit::getCurrency()->getId() . ' ' . $amount . ' donation. These funds are now available in your account and can be used to create new instances or renew/upgrade existing instances.'

                    );

                $Sender->send();

                $MySQLTransaction->commit();

            }
            catch( \Throwable $Throwable ) {

                $MySQLTransaction->rollback();

                throw $Throwable;

            }

        }

        public function canApplyDiscount(): bool {

            return false;

        }

        public function manipulatePrice( Product\Product $Product, Configuration\Configuration $Configuration, Price $Price ): Price {

            return $Price;

        }

        public function modifiesProductName(): bool {

            return true;

        }

        public function getModifiedProductName( Configuration\Configuration $Configuration ): string {

            $Client = Client\Clients::get( $Configuration->getItem('recipient_client_id')->getValue()->get() );

            $amount = $Configuration->getItem('amount')->getValue()->get();

            return 'Donate ' . $amount . ' ' . $this->Currency->getId() . ' to ' . $Client->getEmail()->getAddress()->toString();

        }

        public function canFollowUp(): bool {

            return false;

        }

        public function isPublic(): bool {

            return false;

        }

        /** @return Configuration\Item\Item[] */
        public function getConfigurationItems(): array {

            return [

                new Configuration\Item\Variant\Text\Text('client_id', 'Client id'),
                new Configuration\Item\Variant\Text\Text('recipient_client_id', 'Recipient client id'),
                $this->createAmountOptions()

            ];

        }

        private function createAmountOptions(): Configuration\Item\Variant\Option\Options {

            $Options = new Configuration\Item\Variant\Option\Options('amount', 'Amount');

                $Options->create('5 ' . $this->Currency->getId(), 5)
                    ->setPrice( new Price( 5 ) );

                $Options->create('15 ' . $this->Currency->getId(), 15)
                    ->setPrice( new Price( 15 ) );

                $Options->create('25 ' . $this->Currency->getId(), 25)
                    ->setPrice( new Price( 25 ) );

                $Options->create('50 ' . $this->Currency->getId(), 50)
                    ->setPrice( new Price( 50 ) );

                $Options->create('100 ' . $this->Currency->getId(), 100)
                    ->setPrice( new Price( 100 ) );

            $Options->create('200 ' . $this->Currency->getId(), 200)
                ->setPrice( new Price( 200 ) );

            return $Options;

        }

    }

?>
