<?php

    namespace GameDash\Sdk\Module\Implementation\Service\MountAndBladeWarband\Resources\Statistic;

    use \SimpleXMLElement;
    use \Electrum\Network\AddressUnreachableException;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Network;

    class Statistics extends Implementation\Service\Statistic\Statistics {

        private $Network;
        private $Info;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Network = $Instance->getNetwork();

        }

        public function countConnectedClients(): int {

            try {

                return (int)$this->getInfo()->NumberOfActivePlayers;

            }
            catch( AddressUnreachableException $Exception ) {

                return 0;

            }

        }

        public function getMaxConnectedClients(): int {

            try {

                return (int)$this->getInfo()->MaxNumberOfPlayers;

            }
            catch( AddressUnreachableException $Exception ) {

                return 0;

            }

        }

        private function getInfo(): \SimpleXMLElement {

            if( !$this->Info ) {

                $curlInstance = curl_init();
                curl_setopt($curlInstance, CURLOPT_URL, 'http://' . $this->Network->getIps()->getCurrent()->toString());
                curl_setopt($curlInstance, CURLOPT_PORT, $this->Network->getPorts()->getPrimary()->getNumber());
                curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlInstance, CURLOPT_CONNECTTIMEOUT, 2);

                $result = curl_exec($curlInstance);

                if( curl_errno($curlInstance) !== 0 )  {

                    curl_close($curlInstance);

                    throw new AddressUnreachableException('Server did not reply');

                }

                curl_close($curlInstance);

                $this->Info = new \SimpleXMLElement( $result );

            }

            return $this->Info;

        }

    }

?>
