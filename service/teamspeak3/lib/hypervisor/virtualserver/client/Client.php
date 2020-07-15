<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\Client;

    use function \_\find;
    use function \_\filter;
    use function \_\map;
    use \TeamSpeak3_Node_Client as ApiClient;
    use \TeamSpeak3_Node_Servergroup as ApiServerGroup;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\Group;

    class Client {

        /** @var Hypervisor\Hypervisor */
        private $Hypervisor;

        /** @var VirtualServer\VirtualServer */
        private $VirtualServer;

        /** @var ApiClient */
        private $ApiClient;

        /** @var Client[]|null */
        private $allClients = null;

        public function __construct( Hypervisor\Hypervisor $Hypervisor, VirtualServer\VirtualServer $VirtualServer, ApiClient $ApiClient ) {

            $this->Hypervisor = $Hypervisor;
            $this->VirtualServer = $VirtualServer;
            $this->ApiClient = $ApiClient;

        }

        public function getId(): int {

            return $this->ApiClient->getId();

        }

        public function getName(): string {

            return $this->ApiClient->client_nickname;

        }

        public function hasAvatar(): bool {

            return $this->ApiClient !== null;

        }

        public function downloadAvatar(): ?string {

            return $this->ApiClient->avatarDownload();

        }

        public function ban(): void {

            $this->ApiClient->ban();

        }

        public function kick(): void {

            $this->ApiClient->kick(\TeamSpeak3::KICK_SERVER);

        }

        /** @return Group\Group[] */
        public function getGroups(): array {

            return map(

                filter(

                    $this->VirtualServer->getApi()->serverGroupList(),
                    function( ApiServerGroup $ApiServerGroup ): bool {

                        return find($ApiServerGroup->clientList(), function( array $result ): bool {

                            return (string)$result['client_nickname'] === $this->getName();

                        }) !== null;

                    }

                ),
                function( ApiServerGroup $ApiServerGroup ): Group\Group {

                    return $this->VirtualServer->getGroups()->get( $ApiServerGroup->getId() );

                }

            );

        }

        public function hasGroup( Group\Group $Group ): bool {

            return find($this->getGroups(), static function( Group\Group $_Group ) use ( $Group ): bool { return $Group->getName() === $_Group->getName(); }) !== null;

        }

        public function addGroup( Group\Group $Group ): void {

            $this->ApiClient->addServerGroup( $Group->getId() );

        }

        public function removeGroup( Group\Group $Group ): void {

            $this->ApiClient->remServerGroup( $Group->getId() );

        }

    }

?>
