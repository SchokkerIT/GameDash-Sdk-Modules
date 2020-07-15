<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Schedule\Task\Actions;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Schedule\Task\Action\Parameter;

    class Console extends Implementation\Service\Task\Resource {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get(

                $Gateway->getParameters()->get('instance.id')->getValue()

            );

        }

        public function getTitle(): string {

            return 'Console';

        }

        public function getDescription(): string {

            return 'Execute a console command';

        }

        public function execute( Parameter\ParameterCollection $Parameters ): void {

            $this->Instance->getConsole()->getIo()->getInput()->send(

                $Parameters->get('input')->getValue()

            );

        }

        public function getExecutionCooldown(): int {

            return 0;

        }

        public function getRequiredProcessStatus(): ?bool {

            return true;

        }

        public function getParameters(): Parameter\ParameterCollection {

            $Input = new Parameter\StringParameter('input');

            $Input->setTitle('Input');
            $Input->setIsRequired( true );

            return new Parameter\ParameterCollection([ $Input ]);

        }

    }

?>
