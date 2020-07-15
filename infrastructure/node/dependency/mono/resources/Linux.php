<?php

    namespace GameDash\Sdk\Module\Implementation\Infrastructure\Node\Dependency\Mono\Resources;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Package;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Os;

    class Linux extends Implementation\Infrastructure\Node\Dependency\Dependency {

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Node = Node\Nodes::get( $Gateway->getParameters()->get('node.id')->getValue() );

        }

        public function install(): void {

            $this->getPackage()->install();

        }

        public function uninstall(): void {

            $this->getPackage()->uninstall();

        }

        public function isAvailable(): bool {

            return $this->Node->getOperatingSystems()->getCurrent()->isLinux();

        }

        private function getPackage(): Package\Package {

            return $this->Node->getPackages()->get('mono-complete');

        }

    }

?>
