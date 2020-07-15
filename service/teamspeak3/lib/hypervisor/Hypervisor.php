<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;

    use GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\VirtualServers;
    use function \_\find;
    use function \_\filter;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Setting;

    class Hypervisor {

        private $Node;
        private $Settings;
        private $VirtualServers = null;

        /** @var Connection[] */
        private static $connections = [];

        public function __construct( Node\Node $Node ) {

            $this->Node = $Node;

            $this->Settings = $Node->getSettings();

        }

        public function connect(): Connection {

            if( $this->isConnected() ) {

                throw new Exception('Already connected');

            }

            $Ip = $this->Node->getNetwork()->getIps()->getPrimary();

            $Host = \TeamSpeak3::factory(

                'serverquery://' . $this->getUsername() . ':' . rawurlencode($this->getPassword()) . '@' . $Ip->toString() . ':' . $this->getPort() . '/?'

            );

            $Host->setExcludeQueryClients( true );

            return self::$connections[] = new Connection( $this->Node, $Host );

        }

        public function disconnect(): void {

            if( !$this->isConnected() ) {

                throw new Exception('Not connected');

            }

            self::$connections = filter(self::$connections, function( Connection $Connection ): bool { return $Connection->getNode()->compare( $this->Node ); });

        }

        public function isOnline(): bool {

            try {

                if( $this->isConnected() ) {

                    return true;

                }

                $this->connect();

                return true;

            }
            catch( \TeamSpeak3_Transport_Exception $e ) {

                return false;

            }

        }

        public function isAvailable(): bool {

            return $this->Settings->exists('teamspeak3.hypervisor.isAvailable') && $this->Settings->get('teamspeak3.hypervisor.isAvailable')->getValue() === true;

        }

        public function getVirtualServers(): VirtualServer\VirtualServers {

            return $this->VirtualServers ?? $this->VirtualServers = new VirtualServer\VirtualServers( $this );

        }

        public function getConnection(): Connection {

            if( !$this->isConnected() ) {

                $this->connect();

            }

            return find(self::$connections, function( Connection $Connection ): bool { return $Connection->getNode()->compare( $this->Node ); });

        }

        private function isConnected(): bool {

            return find(self::$connections, function( Connection $Connection ): bool { return $Connection->getNode()->compare( $this->Node ); }) !== null;

        }

        private function getUsername(): string {

            if( $this->Settings->exists('teamspeak3.hypervisor.username') ) {

                return $this->Settings->get('teamspeak3.hypervisor.username')->getValue();

            }

            return 'serveradmin';

        }

        private function getPassword(): string {

            return $this->Settings->get('teamspeak3.hypervisor.password')->getValue();

        }

        private function getPort(): int {

            if( $this->Settings->exists('teamspeak3.hypervisor.port') ) {

                return $this->Settings->get('teamspeak3.hypervisor.port')->getValue();

            }

            return 10011;

        }

    }

?>
