<?php

    namespace GameDash\Sdk\Module\Implementation\Service\CounterStrikeSource\Resources\Network;

    use Electrum\Uri\Uri;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Ip\Ip;
    use \GameDash\Sdk\FFI\Instance;

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

        public function getDirectConnectLink(): Uri {

            $Ip = $this->Instance->getNetwork()->getIps()->getCurrent();

            return Uri::fromString(

                'steam://connect/' . $Ip->toString() . ':' . $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber()

            );

        }

        public function hasDirectConnectLink(): bool {

            return $this->Instance->getNetwork()->getPorts()->getPrimary() !== null;

        }

    }

?>
