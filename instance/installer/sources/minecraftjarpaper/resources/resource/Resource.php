<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarPaper\Resources\Resource;

    use \Electrum\Uri\Uri;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Installer\Record;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarPaper\Lib\Api\Client\Client as ApiClient;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarPaper\Lib\Api\Client\Query\QueryManager as ApiClientQueryManager;

    class Resource extends Implementation\Instance\Installer\Source\Resource\Resource {

        /** @var ApiClientQueryManager */
        private $ApiClientQueryManager;

        public function __construct( Gateway\Gateway $Gateway, string $id ) {

            parent::__construct( $id );

            $this->ApiClientQueryManager = new ApiClientQueryManager( $Gateway );

        }

        public function install( Instance\Instance $Instance, array $options = [] ): array {

            $versionId = $options['versionId'];

            $buildId = $this->getBuildIdFromVersion( $versionId );

            $Destination = $Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $Instance, 'paper_' . $versionId . '.jar' )

            );

            $Destination->downloadFromAsync(

                Uri::fromString(

                    ApiClient::getBaseUri()->toString() . '/paper/' . $versionId . '/' . $buildId . '/download'

                ),
                function( float $percentage ) {

                    $this->getCallbackManager()->onPercentageProgress( 'Downloading', $percentage );

                }

            );

            return [

                $Destination

            ];

        }

        public function uninstall( Instance\Instance $Instance, Record\Record $Record ): void {}

        private function getBuildIdFromVersion( string $id ): int {

            return (int)$this->ApiClientQueryManager->getVersion( $id )['builds']['latest'];

        }

    }

?>
