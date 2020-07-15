<?php

    namespace GameDash\Sdk\Module\Implementation\Infrastructure\Node\Dependency\LibC\Resources;

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

            foreach( $this->getPackages() as $Package ) {

                $Package->install();

            }

        }

        public function uninstall(): void {

            foreach( $this->getPackages() as $Package ) {

                $Package->uninstall();

            }

        }

        public function isAvailable(): bool {

            return $this->Node->getOperatingSystems()->getCurrent()->isLinux();

        }

        /** @return Package\Package[] */
        private function getPackages(): ?array {

            $LinuxRelease = $this->Node->getOperatingSystems()->getCurrent()->getLinuxRelease();

            if( $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::debian() ) || $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::ubuntu() ) ) {

                return [

                    $this->Node->getPackages()->get('lib32gcc1'),
                    $this->Node->getPackages()->get('libc6-dbg'),
                    $this->Node->getPackages()->get('gdb'),
                    $this->Node->getPackages()->get('valgrind')

                ];

            }

            if( $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::centos() ) || $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::rhel() ) ) {

                return [

                    $this->Node->getPackages()->get('glibc.i686'),
                    $this->Node->getPackages()->get('libstdc++.i686'),
                    $this->Node->getPackages()->get('gdb'),
                    $this->Node->getPackages()->get('valgrind')

                ];

            }

            return null;

        }

    }

?>
