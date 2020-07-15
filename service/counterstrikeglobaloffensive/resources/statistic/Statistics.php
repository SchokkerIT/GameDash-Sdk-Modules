<?php

    namespace GameDash\Sdk\Module\Implementation\Service\CounterStrikeGlobalOffensive\Resources\Statistic;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Network;
    use \xPaw\SourceQuery\SourceQuery;

    class Statistics extends Implementation\Service\Statistic\Statistics {

        private $Connection;
        private $queryResult;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $Network = $Instance->getNetwork();

            $this->Connection = new SourceQuery();
            $this->Connection->Connect(

                $Network->getIps()->getCurrent()->toString(),
                $Network->getPorts()->getPrimary()->getNumber()

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
