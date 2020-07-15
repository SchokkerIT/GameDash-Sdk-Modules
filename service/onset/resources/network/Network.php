<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Onset\Resources\Network;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Ip\Ip;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;
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

            $File = $this->ConfigEditor->getFiles()->get(

                new Path\Path( $this->Instance, 'server_config.json' )

            );

            $Settings = $File->getSettings();

                $Settings->getFirst('ipaddress')->setValue( $Ip->toString() );

            $Settings->commit();

        }

        public function unallocateIp(): void {

            $Ip = $this->Instance->getNetwork()->getIps()->getCurrent();

            $this->Instance->getNetwork()->getPorts()->getPrimary()->delete();

            $File = $this->ConfigEditor->getFiles()->get(

                new Path\Path( $this->Instance, 'server_config.json' )

            );

            $Settings = $File->getSettings();

            $Settings->getFirst('ipaddress')->setValue( $Ip->toString() );

            $Settings->commit();

        }

    }

?>
