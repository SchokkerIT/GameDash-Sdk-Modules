<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Shell\Executables\Installer\Source\Resource\SetPreferredBranch\Resources;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Shell\Executable\Result;
    use \GameDash\Sdk\FFI\Instance\Shell\Parameter;

    class Executable extends Implementation\Instance\Shell\Executable\Executable {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get(

                $Gateway->getParameters()->get('instance.id')->getValue()

            );

        }

        public function getDescription(): string {

            return 'Set the preferred branch of a installer source resource';

        }

        public function execute( array $parameters ): ?Result {

            if( !$this->Instance->getInstaller()->getSources()->exists( $parameters['sourceName'] ) ) {

                return new Result([

                    'value' => 'Source does not exist',
                    'isError' => true

                ]);

            }

            $Source = $this->Instance->getInstaller()->getSources()->get( $parameters['sourceName'] );

            if( !$Source->resourceExists( $parameters['resourceId'] ) ) {

                return new Result([

                    'value' => 'Resource does not exist',
                    'isError' => true

                ]);

            }

            $Resource = $Source->getResource( $parameters['resourceId'] );
//
//            if( !$Resource->getBranch( $parameters['branchId'] ) ) {
//
//                return new Result([
//
//                    'value' => 'Branch does not exist',
//                    'isError' => true
//
//                ]);
//
//            }
//
//            $Resource->getSettings()->getAndCreateIfNotFound('preferredBranch')->setValue( $parameters['branchId'] );

            return new Result([

                'value' => 'Successfully set preferred branch to ' . $parameters['branchId'],
                'isSuccess' => true

            ]);

        }

        public function getParameters(): array {

            return [

                new Parameter\Available('sourceName', true),
                new Parameter\Available('resourceId', true),
                new Parameter\Available('branchId', true)

            ];

        }

    }

?>
