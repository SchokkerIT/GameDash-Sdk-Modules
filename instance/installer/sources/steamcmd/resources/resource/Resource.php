<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\SteamCmd\Resources\Resource;

    use function \_\map;
    use \Electrum\Os;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Installer\Record;
    use \GameDash\Sdk\FFI\Infrastructure\Node;
    use \GameDash\Sdk\FFI\Steam\Account\Credentials;
    use \GameDash\Sdk\FFI\Steam\App;

    class Resource
        extends Implementation\Instance\Installer\Source\Resource\Resource
        implements
            Implementation\Instance\Installer\Source\Resource\IStandaloneUpgrade,
            Implementation\Instance\Installer\Source\Resource\IWithBranches

    {

        use Implementation\Instance\Installer\Source\Resource\UseBranchesTrait;

        /** @var Gateway\Gateway */
        private $Gateway;

        /** @var Node\Node */
        private $Node;

        /** @var App\App */
        private $App;

        public function __construct( Gateway\Gateway $Gateway, string $id ) {

            parent::__construct( $id );

            $this->Gateway = $Gateway;

            $this->App = App\Apps::get( $this->getId() );

        }

        public function install( Instance\Instance $Instance, array $options = [] ): array {

            $Diff = $Instance->getFileSystem()->getFiles()->getDiffer()->diffDirectory(

                $Instance->getFileSystem()->getRootDirectory()->getFile()

            );

                $this->installUsingSteamCmd(

                    $Instance, [

                        'asAnonymous' => $options['arguments']['asAnonymous'] ?? false

                    ]

                );

            return $Diff->getChanged();

        }

        public function uninstall( Instance\Instance $Instance, Record\Record $Record ): void {}

        public function upgrade( Instance\Instance $Instance, Record\Record $Record ): array {

            $Diff = $Instance->getFileSystem()->getFiles()->getDiffer()->diffDirectory(

                $Instance->getFileSystem()->getRootDirectory()->getFile()

            );

                $this->installUsingSteamCmd( $Instance );

            return $Diff->getChanged();

        }

        public function getOperatingSystem( Instance\Instance $Instance ): Os\OperatingSystemsEnum {

            $operatingSystems = $this->App->getSupportedOperatingSystems();

            foreach( $operatingSystems as $OperatingSystem ) {

                if( $Instance->getInfrastructure()->getNode()->getOperatingSystems()->getCurrent()->compare( $OperatingSystem ) ) {

                    return $OperatingSystem;

                }

            }

            return $operatingSystems[0];

        }

        public function getCredentials(): ?Credentials {

            if(

                $this->Gateway->getModule()->getSettings()->exists('credentials.username')

                    &&

                $this->Gateway->getModule()->getSettings()->exists('credentials.password')

            ) {

                $Credentials = new Credentials();

                    $Credentials->setUsername( $this->Gateway->getModule()->getSettings()->get('credentials.username')->getValue() );
                    $Credentials->setPassword( $this->Gateway->getModule()->getSettings()->get('credentials.password')->getValue() );

                return $Credentials;

            }

            return null;

        }

        public function getBranches(): array {

            return map($this->App->getBranches()->getAll(), static function( App\Branch\Branch $AppBranch ): Branch {

                $Branch = new Branch( $AppBranch->getName() );

                    $Branch->setIsPrimary( $AppBranch->isPrimary() );

                    if( $AppBranch->getDescription() ) {

                        $Branch->setDescription($AppBranch->getDescription());

                    }

                    if( $AppBranch->requiresPassword() ) {

                        $Branch->setRequiresPassword( $AppBranch->requiresPassword() );

                    }

                    $Branch->setTimeUpdated( $AppBranch->getTimeUpdated() );

                return $Branch;

            });

        }

        public function getVersions(): array {

            $UsedBranch = $this->getUsedBranch();

            $Branch = $UsedBranch ? $this->App->getBranches()->get( $UsedBranch->getId() ) : $this->App->getBranches()->getDefault();

            $LatestVersion = new Version( $Branch->getBuildId() );

            $LatestVersion->setIsLatest( true );

            return [ $LatestVersion ];

        }

        private function installUsingSteamCmd( Instance\Instance $Instance, array $options = [] ): void {

            $asAnonymous = isset( $options['asAnonymous'] ) && $options['asAnonymous'] === true;

            $OperatingSystem = $this->getOperatingSystem( $Instance );

            $Node = $Instance->getInfrastructure()->getNode();

            $SteamCmdSession = $Node->getSteam()->getSteamCmd()->getSessions()->create();

            $SteamCmdSession->setApp( $this->App );

            $SteamCmdSession->setOperatingSystem( $OperatingSystem );

            if( $this->getUsedBranch() ) {

                $SteamCmdSession->setBranch(

                    $this->App->getBranches()->get(

                        $this->getUsedBranch()->getId()

                    )

                );

                if( $this->getBranchPassword() ) {

                    $SteamCmdSession->setBranchPassword( $this->getBranchPassword() );

                }

            }

            if( $asAnonymous ) {

                $SteamCmdSession->setAsAnonymous( true );

            }
            else {

                $Credentials = $this->getCredentials();

                if( $Credentials ) {

                    $SteamCmdSession->setCredentials( $Credentials );

                }

            }

            $SteamCmdSession->setDirectory( $Instance->getFileSystem()->getRootDirectory()->getAbsoluteFile() );

            $SteamCmdSession->getRuntime()->run();

        }

    }

?>
