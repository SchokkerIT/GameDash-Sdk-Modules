<?php

    namespace GameDash\Sdk\Module\Implementation\Service\BattleGroundsThree\Resources\Network;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Ip\Ip;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\ConfigEditor;

    class Network extends Implementation\Service\Network\Network implements Implementation\Service\Network\ICanAllocateIp {

        /** @var Instance\Instance */
        private $Instance;

        /** @var ConfigEditor\ConfigEditor */
        private $ConfigEditor;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );
            $this->ConfigEditor = $this->Instance->getFileSystem()->getConfigEditor();

        }

        public function canAllocateIp(): bool {

            return true;

        }

        public function allocateIp( Ip $Ip ): void {

            $this->Instance->getNetwork()->getPorts()->getPrimary()->delete();

            $this->Instance->getNetwork()->getPorts()->create(

                $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->get(25565)

            )->setIsPrimary( true );

            $Settings = $this->ConfigEditor->getFiles()->get(

                new Instance\FileSystem\Path\Path( $this->Instance, 'server.properties' )

            )->getSettings();

            $Settings->getFirst('server-ip')->setValue( $Ip->toString() );
            $Settings->getFirst('server-port')->setValue( 25565 );

            $Settings->commit();

        }

        public function unallocateIp(): void {

            $Port = $this->Instance->getInfrastructure()->getNode()->getNetwork()->getPorts()->get(25565);

            $Settings = $this->ConfigEditor->getFiles()->get(

                new Instance\FileSystem\Path\Path( $this->Instance, 'server.properties' )

            )->getSettings();

            $Settings->getFirst('server-ip')->setValue( $this->Instance->getNetwork()->getIps()->getCurrent()->toString() );
            $Settings->getFirst('server-port')->setValue( $Port->getNumber() );

            $Settings->commit();

            $this->Instance->getNetwork()->getPorts()->getPrimary()->delete();

            $this->Instance->getNetwork()->getPorts()->create( $Port )->setIsPrimary( true );

        }

    }

?>
