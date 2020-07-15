<?php

    namespace GameDash\Sdk\Module\Implementation\Infrastructure\Node\Dependency\Xvfb\Resources;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \Electrum\Userland\Infrastructure\Node;
    use \Electrum\Userland\Infrastructure\Node\Package;
    use \Electrum\Userland\Infrastructure\Node\Os;

    class Linux extends Implementation\Infrastructure\Node\Dependency\Dependency {

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Node = Node\Nodes::get( $Gateway->getParameters()->get('node.id')->getValue() );

        }

        public function install(): void {

            $Package = $this->getPackage();

            if( $Package ) {

                $Package->install();

            }

        }

        public function uninstall(): void {

            $Package = $this->getPackage();

            if( $Package ) {

                $Package->uninstall();

            }

        }

        public function isAvailable(): bool {

            return $this->Node->getOperatingSystems()->getCurrent()->isLinux();

        }

        private function getPackage(): ?Package\Package {

            $LinuxRelease = $this->Node->getOperatingSystems()->getCurrent()->getLinuxRelease();

            if( $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::debian() ) || $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::ubuntu() ) ) {

                return $this->Node->getPackages()->get('xvfb');

            }

            if( $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::centos() ) || $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::rhel() ) ) {

                return $this->Node->getPackages()->get('xorg-x11-server-Xvfb');

            }

            return null;

        }

    }

?>
