<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\Group;

    use function \_\map;
    use function \_\find;
    use \TeamSpeak3_Node_Servergroup as ApiGroup;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;

    class Groups {

        private $Hypervisor;
        private $VirtualServer;

        /** @var Group[]|null */
        private $allGroups = null;

        public function __construct( Hypervisor\Hypervisor $Hypervisor, VirtualServer\VirtualServer $VirtualServer ) {

            $this->Hypervisor = $Hypervisor;
            $this->VirtualServer = $VirtualServer;

        }

        /** @return Group[] */
        public function getAll(): array {

            if( $this->allGroups === null ) {

                $this->allGroups = map(

                    $this->VirtualServer->getApi()->serverGroupList(),
                    function( ApiGroup $Group ): Group {

                        return new Group($this->Hypervisor, $this->VirtualServer, $Group);

                    }

                );

            }

            return $this->allGroups;

        }

        public function get( int $id ): Group {

            return find($this->getAll(), static function( Group $Group ) use ( $id ): bool { return $Group->getId() === $id; });

        }

        public function getByName( string $name ): Group {

            return find($this->getAll(), static function( Group $Group ) use ( $name ): bool { return $Group->getName() === $name; });

        }

        public function exists( int $id ): bool {

            return find($this->getAll(), static function( Group $Group ) use ( $id ): bool { return $Group->getId() === $id; }) !== null;

        }

    }

?>
