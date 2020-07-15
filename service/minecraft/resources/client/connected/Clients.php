<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Client\Connected;

    use function \_\map;
    use function \_\find;
    use \xPaw\MinecraftQuery;
    use \xPaw\MinecraftQueryException;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\ConfigEditor;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class Clients extends Implementation\Service\Client\Connected\Clients {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Instance\Instance */
        private $Instance;

        /** @var ConfigEditor\File\FIle */
        private $ServerProperties;

        /** @var MinecraftQuery|null */
        private $Connection = null;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );
            $this->ServerProperties = $this->Instance->getFileSystem()->getConfigEditor()->getFiles()->get(

                new Path\Path( $this->Instance, 'server.properties' )

            );

        }

        /** @return Client[] */
        public function getAll(): array {

            $players = $this->createConnection()->getPlayers();

            if( !$players ) {

                return [];

            }

            return map($players, function( $name ): Client {

                return new Client( $this->Gateway, $name );

            });

        }

        public function get( string $name ): Implementation\Service\Client\Connected\Client {

            return find($this->getAll(), static function( Client $Client ) use ( $name ): bool {

                return $Client->getName() === $name;

            });

        }

        public function isAvailable(): bool {

            if( !$this->ServerProperties->exists() ) {

                return false;

            }

            if(

                !$this->ServerProperties->getSettings()->exists('enable-query')

                    ||

                $this->ServerProperties->getSettings()->getFirst('enable-query')->getValue() !== 'true'

                    ||

                !$this->ServerProperties->getSettings()->exists('query.port')

            ) {

                return false;

            }

            try {

                $this->createConnection();

            }
            catch( MinecraftQueryException $exception ) {

                return false;

            }

            return true;

        }

        private function createConnection(): MinecraftQuery {

            if( !$this->Connection ) {

                $Query = new MinecraftQuery;

                $Connect = function( int $port ) use ( $Query ) {

                    $Query->Connect(

                        $this->Instance->getNetwork()->getIps()->getCurrent()->toString(),

                        $port

                    );

                };

                $Connect(

                    (int)$this->ServerProperties->getSettings()->getFirst('query.port')->getValue()

                );

                $this->Connection = $Query;

            }

            return $this->Connection;

        }

    }

?>
