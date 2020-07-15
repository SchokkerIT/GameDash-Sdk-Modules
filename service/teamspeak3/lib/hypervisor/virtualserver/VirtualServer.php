<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;

    use \TeamSpeak3_Node_Server as Api;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;

    class VirtualServer {

        private $Hypervisor;
        private $Clients = null;
        private $Groups = null;
        private $PrivilegeKeys = null;
        private $Api = null;

        private $id;

        public function __construct( Hypervisor\Hypervisor $Hypervisor, int $id ) {

            $this->Hypervisor = $Hypervisor;

            $this->id = $id;

        }

        public function getId(): int {

            return $this->id;

        }

        public function start(): void {

            $this->Hypervisor->getConnection()->getInstance()->serverStart( $this->getId() );

        }

        public function stop(): void {

            $this->Hypervisor->getConnection()->getInstance()->serverStop( $this->getId() );

        }

        public function isOnline(): bool {

            try {

                return $this->getApi()->isOnline();

            }
            catch( \Exception $Exception ) {

                if( $Exception->getMessage() === 'server is not running' || $Exception->getCode() === 111 ) {

                    return false;

                }

                throw new \Exception( $Exception->getMessage() );

            }

        }

        public function delete(): void {

            if( $this->isOnline() ) {

                $this->stop();

            }

            $this->getApi()->delete();

        }

        public function getGroups(): Group\Groups {

            return $this->Groups ?? $this->Groups = new Group\Groups( $this->Hypervisor, $this );

        }

        public function getPrivilegeKeys(): PrivilegeKey\PrivilegeKeys {

            return $this->PrivilegeKeys ?? $this->PrivilegeKeys = new PrivilegeKey\PrivilegeKeys( $this->Hypervisor, $this );

        }

        public function getClients(): Client\Clients {

            return $this->Clients ?? $this->Clients = new Client\Clients( $this->Hypervisor, $this );

        }

        public function createSnapshot(): string {

            return $this->getApi()->snapshotCreate();

        }

        public function getApi(): Api {

            return $this->Api ?? $this->Api = $this->Hypervisor->getConnection()->getInstance()->serverGetById( $this->getId() );

        }

    }

?>
