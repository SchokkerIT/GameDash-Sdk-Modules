<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Settings\Plugins\JavaVersions\Resources;

    use function \_\map;
    use function \_\find;
    use function \_\filter;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Dependency;

    class JavaVersions extends Implementation\Instance\Settings\Plugin {

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Node = $Instance->getInfrastructure()->getNode();

        }

        public function getData(): array {

            return [

                'options' => map($this->getVersions(), static function( Dependency\Dependency $Dependency ) {

                    return [

                        'title' => $Dependency->getTitle(),
                        'value' => $Dependency->getName()

                    ];

                })

            ];

        }

        public function validateValue( $value ): bool {

            return find($this->getData()['options'], static function( array $option ) use ( $value ): bool {

                return $value === $option['value'];

            }) !== null;

        }

        public function isAvailable(): bool {

            return count( $this->getVersions() ) > 1;

        }

        /** @return Dependency\Group\Item[] */
        private function getVersions(): array {

            $JdkGroup = $this->Node->getDependencies()->getGroups()->get('jdk');

            if( !$JdkGroup ) {

                return [];

            }

            return map(

                filter(

                    $JdkGroup->getItems(),
                    function( Dependency\Group\Item $GroupItem ) {

                        return $GroupItem->getDependency()->getSetup( $this->Node )->isInstalled();

                    }

                ),
                static function( Dependency\Group\Item $Item ): Dependency\Dependency {

                    return $Item->getDependency();

                }

            );

        }

    }

?>
