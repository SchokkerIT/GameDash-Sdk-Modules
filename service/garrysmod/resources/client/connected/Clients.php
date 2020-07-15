<?php

    namespace GameDash\Sdk\Module\Implementation\Service\GarrysMod\Resources\Client\Connected;

    use function \_\map;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \xPaw\SourceQuery\SourceQuery;

    class Clients extends Implementation\Service\Client\Connected\Clients {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var SourceQuery */
        private $Connection;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        /** @return Client[] */
        public function getAll(): array {

            return map($this->createConnection()->GetPlayers(), function( array $player ) {

                return $this->get( $player['Name'] );

            });

        }

        public function get( string $name ): Implementation\Service\Client\Connected\Client {

            return new Client( $this->Gateway, $name );

        }

        public function isAvailable(): bool {

            return true;

        }

        private function createConnection(): SourceQuery {

            if( !$this->Connection ) {

                $this->Connection = new SourceQuery();
                $this->Connection->Connect(

                    $this->Instance->getNetwork()->getIps()->getCurrent()->toString(),
                    $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber()

                );

            }

            return $this->Connection;

        }

    }

?>
