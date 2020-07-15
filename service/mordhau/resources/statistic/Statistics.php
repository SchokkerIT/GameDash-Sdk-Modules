<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Mordhau\Resources\Statistic;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use xPaw\SourceQuery\SourceQuery;

    class Statistics extends Implementation\Service\Statistic\Statistics {

        private $Connection;
        private $info = null;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Connection = new SourceQuery();
            $this->Connection->Connect(

                $Instance->getNetwork()->getIps()->getCurrent()->toString(),
                $Instance->getNetwork()->getPorts()->getByName('query')->getNumber()

            );

        }

        public function countConnectedClients(): int {

            return $this->getInfo()['Players'];

        }

        public function getMaxConnectedClients(): int {

            return $this->getInfo()['MaxPlayers'];

        }

        private function getInfo() {

            if( !$this->info ) {

                $this->info = $this->Connection->GetInfo();

            }

            return $this->info;

        }

    }

?>
