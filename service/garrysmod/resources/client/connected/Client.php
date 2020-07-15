<?php

    namespace GameDash\Sdk\Module\Implementation\Service\GarrysMod\Resources\Client\Connected;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;

    class Client extends Implementation\Service\Client\Connected\Client {

        /** @var Instance\Instance */
        private $Instance;

        /** @var string */
        private $name;

        public function __construct( Gateway\Gateway $Gateway, string $name ) {

            $this->name = $name;

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

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
