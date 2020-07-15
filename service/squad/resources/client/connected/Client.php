<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Squad\Resources\Client\Connected;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;

    class Client extends Implementation\Service\Client\Connected\Client {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var FFI\Instance\Instance */
        private $Instance;

        /** @var string */
        private $name;

        public function __construct( Gateway\Gateway $Gateway, string $name ) {

            $this->Gateway = $Gateway;

            $this->name = $name;

            $this->Instance = new FFI\Instance\Instance( $this->Gateway->getParameters()->get('instance.id')->getValue() );

        }

        public function getId(): string {

            return $this->name;

        }

        public function getName(): string {

            return $this->name;

        }

        public function hasImage(): bool {

            return true;

        }

        public function getActions(): array {

            $actions = [

                $this->Gateway->getHelpers()->get('createClientAction')->execute([

                    $this, 'kick', 'Kick', function() {

                        $this->Instance->getConsole()->getIo()->getInput()->send('kick ' . $this->getName());

                    }

                ])

            ];

            return $actions;

        }

    }

?>
