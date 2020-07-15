<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Legal;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \Electrum\Uri\Uri;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance\Legal\Agreement\StaticAgreement;

    class Legal extends Implementation\Service\Legal\Legal {

        public function __construct( Gateway\Gateway $Gateway ) {}

        public function getAvailableAgreements(): array {

            $EulaAgreement = new StaticAgreement('minecraft-server-eula', 'Minecraft Server EULA');

            $EulaAgreement->setUri( Uri::fromString('https://account.mojang.com/documents/minecraft_eula') );

            return [ $EulaAgreement ];

        }

    }

?>
