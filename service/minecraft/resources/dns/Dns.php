<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Dns;

    use \Electrum\Enums;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\FFI\Instance\Dns\Domain;
    use \GameDash\Sdk\FFI\Instance\Dns\Record;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;

    class Dns extends Implementation\Service\Dns\Dns {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = new Instance\Instance( $Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function createPrimaryRecord( Domain\Domain $Domain, string $name ): Record\Record {

            return $this->Instance->getDns()->getRecords()->createSrv(

                $Domain,
                $name,
                'minecraft',
                Enums\Network\Ip\Protocols::tcp(),
                $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber(),
                0,
                0

            );

        }

    }

?>
