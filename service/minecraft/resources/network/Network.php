<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Network;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Ip\Ip;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;
    use \GameDash\Sdk\FFI\Instance\FileSystem\ConfigEditor;
    use \GameDash\Sdk\FFI\Instance\Dns;

    class Network extends Implementation\Service\Network\Network implements Implementation\Service\Network\ICanAllocateIp {

        /** @var Instance\Instance */
        private $Instance;

        /** @var FileSystem\FileSystem */
        private $FileSystem;

        /** @var ConfigEditor\ConfigEditor */
        private $ConfigEditor;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );
            $this->FileSystem = $this->Instance->getFileSystem();
            $this->ConfigEditor = $this->Instance->getFileSystem()->getConfigEditor();

        }

        public function allocateIp( Ip $Ip ): void {

            $this->Instance->getNetwork()->getPorts()->getPrimary()->delete();

            $PrimaryPort = $this->Instance->getNetwork()->getPorts()->create( 25565 );

            $PrimaryPort->setIsPrimary( true );

            $Settings = $this->ConfigEditor->getFiles()->get(

                new Instance\FileSystem\Path\Path( $this->Instance, 'server.properties' )

            )->getSettings();

            $Settings->getFirst('server-ip')->setValue( $Ip->toString() );
            $Settings->getFirst('server-port')->setValue( 25565 );

            $Settings->commit();

        }

        public function unallocateIp(): void {

            $File = $this->ConfigEditor->getFiles()->get(

                new Instance\FileSystem\Path\Path( $this->Instance, 'server.properties' )

            );

            if( $File->exists() ) {

                $Settings = $File->getSettings();

                $Settings->getFirst('server-ip')->setValue($this->Instance->getNetwork()->getIps()->getCurrent()->toString());
                $Settings->getFirst('server-port')->setValue(25565);

                $Settings->commit();

            }

            $this->Instance->getNetwork()->getPorts()->getPrimary()->delete();

            $PrimaryPort = $this->Instance->getNetwork()->getPorts()->create( 25565 );

            $PrimaryPort->setIsPrimary( true );

        }

        public function allocatePorts(): void {

            $Path = new Path\Path( $this->Instance, 'server.properties' );

            $File = $this->FileSystem->getConfigEditor()->getFiles()->get( $Path );

            if( $File->exists() ) {

                $Settings = $File->getSettings();

                $port = $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber();
                $queryPort = $this->Instance->getNetwork()->getPorts()->getByName('query')->getNumber();

                if( !$Settings->exists('query.port') ) {

                    $Settings->add( $Settings->createInstance('query.port', $queryPort) );

                }
                else {

                    $Settings->getFirst('query.port')->setValue( $queryPort );

                }

                if( !$Settings->exists('server-port') ) {

                    $Settings->add( $Settings->createInstance('server-port', $port) );

                }
                else {

                    $Settings->getFirst('server-port')->setValue( $port );

                }

                $Settings->commit();

            }

        }

        public function requiresPrimaryPortForConnection(): bool {

            $PrimaryDnsRecord = $this->Instance->getDns()->getRecords()->getPrimary();

            return !$PrimaryDnsRecord || !$PrimaryDnsRecord->getType()->compare( Dns\Record\TypesEnum::SRV() );

        }

    }

?>
