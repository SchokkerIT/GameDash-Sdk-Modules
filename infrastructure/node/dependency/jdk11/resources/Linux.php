<?php

    namespace GameDash\Sdk\Module\Implementation\Infrastructure\Node\Dependency\JDK11\Resources;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Infrastructure\Node\FileSystem\File;
    use \GameDash\Sdk\FFI\Infrastructure\Node\FileSystem\Path;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Package;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Os;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Dependency;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Dependency\Group;

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

        public function getInstallLocation(): File\File {

            return $this->Node->getFileSystem()->getFiles()->get(

                new Path\Path(

                    $this->Node,
                    $this->Node->getOperatingSystems()->getCurrent()->getLinuxRelease()->distroIsDebianBased() ?

                        '/usr/lib/jvm/java-11-openjdk-amd64'

                        :

                        '/usr/lib/jvm/java-11'

                )

            );

        }

        /** @return Group\Group[] */
        public function getGroups(): array {

            $Group = new Group\Group('jdk');

                $Item = $Group->createItem( Dependency\Dependencies::get('jdk11') );

                if( Dependency\Dependencies::get('jdk11')->getSetup( $this->Node )->isInstalled() ) {

                    $Item->setIsPrimary( true );

                }

            return [ $Group ];

        }

        private function getPackage(): ?Package\Package {

            $LinuxRelease = $this->Node->getOperatingSystems()->getCurrent()->getLinuxRelease();

            if( $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::debian() ) || $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::ubuntu() ) ) {

                return $this->Node->getPackages()->get('openjdk-11-jdk');

            }

            if( $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::centos() ) || $LinuxRelease->getDistro()->compare( Os\Linux\DistroEnum::rhel() ) ) {

                return $this->Node->getPackages()->get('java-11-openjdk');

            }

            return null;

        }

    }

?>
