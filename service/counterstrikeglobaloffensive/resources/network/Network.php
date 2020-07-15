<?php

    namespace GameDash\Sdk\Module\Implementation\Service\CounterStrikeGlobalOffensive\Resources\Network;

    use \Electrum\Uri\Uri;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Ip\Ip;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class Network

        extends Implementation\Service\Network\Network
        implements Implementation\Service\Network\ICanAllocateIp,Implementation\Service\Network\IHasDirectConnectLink

    {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function canAllocateIp(): bool {

            return true;

        }

        public function allocateIp( Ip $Ip ): void {

            $this->Instance->getNetwork()->getPorts()->getPrimary()->delete();

            $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->get( 27015 )

            )->setIsPrimary( true );

        }

        public function unallocateIp(): void {

            $this->Instance->getNetwork()->getPorts()->getPrimary()->delete();

            $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->getRandomFree()

            )->setIsPrimary( true );

        }

        public function hasDirectConnectLink(): bool {

            return $this->Instance->getNetwork()->getPorts()->getPrimary() !== null;

        }

        public function getDirectConnectLink(): Uri {

            $Ip = $this->Instance->getNetwork()->getIps()->getCurrent();

            $password = $this->getPassword();

            return Uri::fromString(

                'steam://connect/' . $Ip->toString() . ':' . $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber() . ( $password ? '/' . $password : '' )

            );

        }

        private function getPassword(): ?string {

            $File = $this->Instance->getFileSystem()->getConfigEditor()->getFiles()->get(

                new Path\Path( $this->Instance, 'csgo/cfg/server.cfg' )

            );

            if( !$File->exists() ) {

                return null;

            }

            if( !$File->getSettings()->exists('sv_password') ) {

                return null;

            }

            $password = $File->getSettings()->getFirst('sv_password')->getValue();

            if( $password === '""' ) {

                return null;

            }

            return $password;

        }

    }

?>
