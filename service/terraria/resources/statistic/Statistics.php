<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Terraria\Resources\Statistic;

    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \Electrum\Enums\Network\Http\Protocols as HttpProtocolsEnum;
    use \Electrum\Uri\Uri;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Http;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Network;

    class Statistics extends Implementation\Service\Statistic\Statistics {

        /** @var Network\Network */
        private $Network;

        /** @var array */
        private $queryResult;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Network = $Instance->getNetwork();

        }

        public function countConnectedClients(): int {

            return $this->query()['playercount'];

        }

        public function getMaxConnectedClients(): int {

            return $this->query()['maxplayers'];

        }

        private function query(): array {

            if( !$this->queryResult ) {

                $Request = Http\Client\Client::createRequest(

                    HttpMethodsEnum::get(),
                    Uri::build(

                        HttpProtocolsEnum::http(),
                        $this->Network->getIps()->getCurrent()->toString(),
                        $this->Network->getPorts()->getByName('restapi')->getNumber()

                    )

                );

                $Request->send();

                $this->queryResult = $Request->getResponse()->getAsJson();

            }

            return $this->queryResult;

        }

    }

?>
