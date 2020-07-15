<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Spiget\Resources\Resource;

    use \Electrum\Uri\Uri;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Instance\Installer\Record;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Spiget\Lib\Api\Client\Client as ApiClient;

    class Resource extends Implementation\Instance\Installer\Source\Resource\Resource {

        public function install( Instance\Instance $Instance, array $options = [] ): array {

            $versionId = $options['versionId'] ?? null;

            $Version = $versionId ? $this->getVersion( $versionId ) : null;

            $fileName = preg_replace("/(\W)+/", '', $this->getTitle() . ( $Version ? '_' . $Version->getId() : '' )) . '.jar';

            $Destination = $Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $Instance, '/plugins/' . $fileName )

            );

            $Destination->downloadFromAsync(

                Uri::fromString(

                    ApiClient::getBaseUri()->toString() . '/resources/' . $this->getId() . '/download' . ( $Version ? '?version=' . $Version->getId() : null )

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

    }

?>
