<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Onset\Resources\Statistic;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use OGP\OGP;

    require_once __BASEDIR . '/userland/lib/ogp/OGP.php';

    class Statistics extends Implementation\Service\Statistic\Statistics {

        /** @var OGP */
        private $Connection;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $Network = $Instance->getNetwork();

            $Network->getPorts()->getPrimary();

            $this->Connection = new OGP( $Network->getIps()->getCurrent()->toString(), $Network->getPorts()->getByName('query')->getNumber() );

            if( !$this->Connection->getStatus() ) {

                throw new \Exception( $this->Connection->error );

            }

        }

        public function countConnectedClients(): int {

            return $this->Connection->serverInfo['PlayerCount'];

        }

        public function getMaxConnectedClients(): int {

            return $this->Connection->serverInfo['SlotMax'];

        }

    }

?>
