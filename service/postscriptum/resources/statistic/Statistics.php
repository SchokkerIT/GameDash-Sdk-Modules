<?php

    namespace GameDash\Sdk\Module\Implementation\Service\PostScriptum\Resources\Statistic;

    use \xPaw\SourceQuery\SourceQuery;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;

    class Statistics extends Implementation\Service\Statistic\Statistics {

        /** @var SourceQuery */
        private $Connection;

        /** @var array|null */
        private $queryResult;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $Network = $Instance->getNetwork();

            $Network->getPorts()->getPrimary();

            $this->Connection = new SourceQuery();
            $this->Connection->Connect(

                $Instance->getNetwork()->getIps()->getCurrent()->toString(),
                $Instance->getNetwork()->getPorts()->getByName('query')->getNumber(),
                1,
                'SourceQuery :: SOURCE'

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

                $this->queryResult = $this->Connection->GetInfo();

            }

            return $this->queryResult;

        }

    }

?>
