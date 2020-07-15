<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Statistic;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \xPaw\MinecraftPing;

    class Statistics extends Implementation\Service\Statistic\Statistics {

        /** @var MinecraftPing */
        private $Connection;

        /** @var array|null */
        private $queryResult = null;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $Network = $Instance->getNetwork();

            $Network->getPorts()->getPrimary();

            $this->Connection = new MinecraftPing(

                $Network->getIps()->getCurrent()->toString(),
                $Network->getPorts()->getPrimary()->getNumber()

            );

        }

        public function countConnectedClients(): int {

            return $this->query()['players']['online'];

        }

        public function getMaxConnectedClients(): int {

            return $this->query()['players']['max'];

        }

        private function query(): array {

            if( !$this->queryResult ) {

                $this->queryResult = $this->Connection->query();

            }

            return $this->queryResult;

        }

    }

?>
