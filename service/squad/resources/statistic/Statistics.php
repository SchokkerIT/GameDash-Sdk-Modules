<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Squad\Resources\Statistic;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Network;
    use \xPaw\SourceQuery\SourceQuery;

    class Statistics extends Implementation\Service\Statistic\Statistics {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Instance\Instance */
        private $Instance;

        /** @var Network\Network */
        private $Network;

        /** @var SourceQuery */
        private $Connection;

        /** @var array */
        private $queryResult;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = new FFI\Instance\Instance( $this->Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Network = $this->Instance->getNetwork();

            $this->Connection = new SourceQuery();
            $this->Connection->Connect(

                $this->Instance->getNetwork()->getIps()->getCurrent()->toString(),
                $this->Instance->getNetwork()->getPorts()->getByName('query')->getNumber()

            );

        }

        public function countConnectedClients(): int {

            return $this->query()['Players'];

        }

        public function getMaxConnectedClients(): int {

            return $this->query()['MaxPlayers'];

        }

        private function query(): array {

            if( !$this->queryResult ) {

                $result = $this->Connection->GetInfo();

                if( !$result ) {

                    throw new \Exception('Could not query server');

                }

                $this->queryResult = $result;

            }

            return $this->queryResult;

        }

    }

?>
