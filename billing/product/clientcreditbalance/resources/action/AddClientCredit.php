<?php

    namespace GameDash\Sdk\Module\Implementation\Billing\Product\ClientCreditBalance\Resources\Action;

    use \Electrum\Database;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \Electrum\Userland\Sdk\Module\Common;
    use \GameDash\Sdk\FFI\Client;
    use \GameDash\Sdk\FFI\Billing\Price\Price;
    use \GameDash\Sdk\FFI\Billing\Subscription;
    use \GameDash\Sdk\FFI\Billing\Transaction;
    use \GameDash\Sdk\FFI\Billing\Currency;
    use \GameDash\Sdk\FFI\Billing\Product;
    use \GameDash\Sdk\FFI\Billing\Product\Action\Configuration;

    class AddClientCredit implements Implementation\Billing\Product\Action\IAction,Implementation\Billing\Product\Action\IFollowUp {

        /** @var Currency\Currency */
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

                $Client->getBilling()->getCredit()->addBalance(

                    (float)$Configuration->getItem('amount')->getValue()->get()

                );

                $MySQLTransaction->commit();

            }
            catch( \Throwable $Throwable ) {

                $MySQLTransaction->rollback();

                throw $Throwable;

            }

        }

        public function manipulatePrice( Product\Product $Product, Configuration\Configuration $Configuration, Price $Price ): Price {

            return $Price;

        }

        public function modifiesProductName(): bool {

            return true;

        }

        public function canApplyDiscount(): bool {

            return false;

        }

        public function getModifiedProductName( Configuration\Configuration $Configuration ): string {

            $amount = $Configuration->getItem('amount')->getValue()->get();

            return 'Add ' . $amount . ' ' . $this->Currency->getId() . ' to credit balance';

        }

        public function canFollowUp(): bool {

            return true;

        }

        public function followUp( Client\Client $Client, Transaction\Transaction $Transaction ): void {

            $Sender = $Client->getEmail()->createSender();

            $Sender->setTitle('How\'s your new game server?');

            $Sender->setMessage('
            
                You bought a server from us some time ago, and we wanted to ask you what you think of our service so far. If you can spare a few minutes, we would appreciate it if you can leave a review on our <a href="https://www.trustpilot.com/review/oasis-hosting.net">Trustpilot</a> page. Thanks!
            
            ');

            $Sender->send();

        }

        public function isPublic(): bool {

            return false;

        }

        /** @return Configuration\Item\Item[] */
        public function getConfigurationItems(): array {

            return [

                new Configuration\Item\Variant\Text\Text('client_id', 'Client id'),
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

                $Options->create('150 ' . $this->Currency->getId(), 150)
                    ->setPrice( new Price( 150 ) );

                $Options->create('200 ' . $this->Currency->getId(), 200)
                    ->setPrice( new Price( 200 ) );

                $Options->create('250 ' . $this->Currency->getId(), 250)
                    ->setPrice( new Price( 250 ) );

            return $Options;

        }

    }

?>
