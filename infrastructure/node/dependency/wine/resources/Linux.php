<?php

    namespace GameDash\Sdk\Module\Implementation\Infrastructure\Node\Dependency\Wine\Resources;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \Electrum\Userland\Infrastructure\Node;
    use \Electrum\Userland\Infrastructure\Node\Package;

    class Linux extends Implementation\Infrastructure\Node\Dependency\Dependency {

        /** @var Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Node = Node\Nodes::get( $Gateway->getParameters()->get('node.id')->getValue() );

        }

        public function install(): void {

            $ChildProcess = $this->Node->getProcesses()->getChildProcesses()->create();

            $Command = $ChildProcess->getHelpers()->getCommand('dpkg --add-architecture i386 && wget -nc https://dl.winehq.org/wine-builds/Release.key && apt-key add Release.key && rm Release.key && apt-get install --yes --force-yes software-properties-common && apt-add-repository https://dl.winehq.org/wine-builds/ubuntu/ && apt-get update && apt-get --yes --force-yes install --install-recommends winehq-devel');

            $ChildProcess->setExecutable( $Command->getExecutable() );
            $ChildProcess->setArgs( $Command->getArgs() );

            $ChildProcess->spawn(

                [

                    'await' => true

                ]

            );

        }

        public function uninstall(): void {

            $this->getPackage()->uninstall();

        }

        public function isAvailable(): bool {

            $OperatingSystem = $this->Node->getOperatingSystems()->getCurrent();

            return $OperatingSystem->isLinux() && $OperatingSystem->getLinuxRelease()->distroIsDebianBased();

        }

        private function getPackage(): Package\Package {

            return $this->Node->getPackages()->get('winehq-devel');

        }

    }

?>
