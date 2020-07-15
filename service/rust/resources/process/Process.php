<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Rust\Resources\Process;

    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Infrastructure\Node\Process\ChildProcess\ChildProcessNotRunningException;

    class Process extends Implementation\Service\Process\Process {

        /** @var Instance\Instance */
        private $Instance;

        /** @var Instance\Process\Process */
        private $Process;

        /** @var Instance\Infrastructure\Node\Node */
        private $Node;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get( $Gateway->getParameters()->get('instance.id')->getValue() );

            $this->Process = $this->Instance->getProcess();
            $this->Node = $this->Instance->getInfrastructure()->getNode();

        }

        public function start(): void {

            $ChildProcess = $this->Node->getProcesses()->getChildProcesses()->create();

            $ChildProcess->setArgs([

                '-batchmode',
                '-server.ip',
                $this->Instance->getNetwork()->getIps()->getCurrent()->toString(),
                '-server.port',
                $this->Instance->getNetwork()->getPorts()->getPrimary()->getNumber(),
                '-rcon.ip',
                $this->Instance->getNetwork()->getIps()->getCurrent()->toString(),
                '-rcon.port',
                $this->Instance->getNetwork()->getPorts()->getByName('rcon')->getNumber(),
                '-rcon.password',
                $this->Instance->getSettings()->get('rcon_password')->getValue(),
                '-server.maxplayers',
                $this->Instance->getSettings()->get('maxConnectedClients')->getValue(),
                '-server.hostname',
                '"' . $this->Instance->getName()->getValue() . '"',
                '-server.level',
                '"' . $this->Instance->getSettings()->get('level')->getValue() . '"',
                '-server.description',
                '"' . $this->Instance->getSettings()->get('description')->getValue() . '"',
                '-server.headerimage',
                $this->Instance->getSettings()->get('header_image')->getValue(),
                '-server.url',
                '"' . $this->Instance->getSettings()->get('server_url')->getValue() . '"'

            ]);

            if( $this->Node->getOperatingSystems()->getCurrent()->isLinux() ) {

                $ldLibraryPath = (

                    $this->Node->getSystem()->getEnvironment()->getVariables()->exists('LD_LIBRARY_PATH') ?

                        $this->Node->getSystem()->getEnvironment()->getVariables()->get('LD_LIBRARY_PATH')->getValue()

                        :

                        null

                );

                $Directory = $this->Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile();

                $ChildProcess->addEnvironmentVariable(

                    'LD_LIBRARY_PATH', $Directory->getPath()->toString() . '/RustDedicated_Data/Plugins/x86_64/:' . $ldLibraryPath

                );

                $ChildProcess->setExecutable('./RustDedicated');

            }
            else if( $this->Node->getOperatingSystems()->getCurrent()->isWindows() ) {

                $ChildProcess->setExecutable('RustDedicated.exe');

            }

            $ChildProcess->spawn();

        }

        public function stop(): void {

            try {

                $ChildProcess = $this->Process->getChildProcesses()->getCurrent();

                $ChildProcess->stop();

            }
            catch( ChildProcessNotRunningException $e ) {}

        }

        public function restart(): void {}

        public function isOnline(): bool {

            return $this->Process->hasId() && $this->Node->getProcesses()->getChildProcesses()->get( $this->Process->getId() )->exists();

        }

    }

?>
