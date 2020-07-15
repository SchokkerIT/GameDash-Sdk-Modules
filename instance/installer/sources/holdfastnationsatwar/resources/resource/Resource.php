<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\HoldfastNationsAtWar\Resources\Resource;

    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Installer\Record;

    class Resource extends Implementation\Instance\Installer\Source\Resource\Resource {

        public function install( Instance\Instance $Instance, array $options = [] ): array {

            $Node = $Instance->getInfrastructure()->getNode();

            $App = $Node->getSteam()->getApps()->get(589290);

            $Workshop = $App->getWorkshop();

            $Session = $Workshop->getItems()->getInstallManager()->getSessions()->create();

            $Session->addItem( $App->getWorkshop()->getItems()->get( $this->getId() ) );

            $Results = $Session->install();

            $Diff = $Instance->getFileSystem()->getFiles()->getDiffer()->diffDirectory( $Instance->getFileSystem()->getRootDirectory()->getFile() );

                foreach( $Results->getAll() as $Result ) {

                    $DestinationDirectory = $Node->getFileSystem()->getFiles()->get(

                        $Instance->getFileSystem()->getPaths()->create('mods/content/' . $App->getId() . '/' . $Result->getItem()->getId())->getAbsolute()

                    );

                    if( !$DestinationDirectory->exists() ) {

                        $DestinationDirectory->makeDirectory();

                    }

                    $FileGroup = $Node->getFileSystem()->getFiles()->createGroup();

                    $FileGroup->addFiles(

                        $FileGroup->createFiles( $Result->getDirectory()->getDirectoryContents() )

                    );

                    $FileGroup->copy( $DestinationDirectory );

                }

            return $Diff->getChanged();

        }

        public function uninstall( Instance\Instance $Instance, Record\Record $Record ): void {}

    }

?>
