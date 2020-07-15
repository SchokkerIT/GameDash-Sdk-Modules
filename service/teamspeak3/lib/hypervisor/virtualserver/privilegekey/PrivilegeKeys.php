<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\PrivilegeKey;

    use function \_\find;
    use function \_\map;
    use \Electrum\Time\Time;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;

    class PrivilegeKeys {

        private $Hypervisor;
        private $VirtualServer;

        /** @var PrivilegeKey[] */
        private $privilegeKeys = [];

        public function __construct( Hypervisor\Hypervisor $Hypervisor, VirtualServer\VirtualServer $VirtualServer ) {

            $this->Hypervisor = $Hypervisor;
            $this->VirtualServer = $VirtualServer;

        }

        /** @return PrivilegeKey[] */
        public function getAll(): array {

            return map(

                $this->VirtualServer->getApi()->privilegeKeyList(),
                static function( array $result, string $key ): PrivilegeKey {

                    return new PrivilegeKey(

                        $key, $result['token_description'], Time::createFromTimestamp( $result['token_created'] )

                    );

                }

            );

        }

        public function get( string $key ) {

            return find($this->getAll(), static function( PrivilegeKey $PrivilegeKey ) use ( $key ): bool { return $PrivilegeKey->getKey() === $key; });

        }

        public function exists( string $key ): bool {

            return find($this->getAll(), static function( PrivilegeKey $PrivilegeKey ) use ( $key ): bool { return $PrivilegeKey->getKey() === $key; }) !== null;

        }

    }

?>
