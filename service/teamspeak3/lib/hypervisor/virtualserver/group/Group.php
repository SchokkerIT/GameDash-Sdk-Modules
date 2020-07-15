<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\Group;

    use function \_\find;
    use function \_\filter;
    use \TeamSpeak3_Node_Servergroup as ApiGroup;
    use \GameDash\Sdk\FFI\Company;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\PrivilegeKey;

    class Group {

        private $Hypervisor;
        private $VirtualServer;
        private $ApiGroup;

        public function __construct( Hypervisor\Hypervisor $Hypervisor, VirtualServer\VirtualServer $VirtualServer, ApiGroup $ApiGroup ) {

            $this->Hypervisor = $Hypervisor;
            $this->VirtualServer = $VirtualServer;
            $this->ApiGroup = $ApiGroup;

        }

        public function getId(): int {

            return $this->ApiGroup->getId();

        }

        public function getName(): string {

            return (string)$this->ApiGroup;

        }

        public function createPrivilegeKey(): PrivilegeKey\PrivilegeKey {

            return $this->VirtualServer->getPrivilegeKeys()->get(

                $this->ApiGroup->privilegeKeyCreate('Privilege key generated by ' . Company\Company::getTradingName())

            );

        }

        public function hasPermission( string $id ): bool {

            return find(

                $this->ApiGroup->permList(),
                static function( string $_id ) use ( $id ): bool {

                    return $_id === $id;

                }

            ) !== null;

        }

        public function assignPermission( string $id, string $value ): void {

            $this->ApiGroup->permAssign( $id, $value );

        }

        public function removePermission( string $id ): void {

            $this->ApiGroup->permRemove( $id );

        }

    }

?>
