<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Squad\Resources\Task\Resources;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance\Task\Parameter;

    class Console extends Implementation\Service\Task\Resource {

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var FFI\Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

            $this->Instance = new FFI\Instance\Instance(

                $this->Gateway->getParameters()->get('instance.id')->getValue()

            );
        }

        public function getTitle(): string {

            return 'Console';

        }

        public function getDescription(): string {

            return 'Execute a console command at the specified time(s)';

        }

        public function execute( array $parameters ): void {

            $this->Instance->getConsole()->getIo()->getInput()->send( $parameters['input'] );

        }

        public function getExecutionCooldown(): int {

            return 0;

        }

        public function getAvailableParameters(): array {

            $Input = new Parameter\Available('input', 'Input');

            $Input->setIsRequired( true );

            return [

                $Input

            ];

        }

    }

?>
