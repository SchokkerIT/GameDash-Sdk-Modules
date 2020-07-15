<?php

    namespace GameDash\Sdk\Module\Implementation\Service\Minecraft\Resources\Shell\Executables;

    use \Electrum\Uri\Uri;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Base\Implementation;
    use \GameDash\Sdk\FFI;
    use \GameDash\Sdk\FFI\Instance;
    use \GameDash\Sdk\FFI\Http;
    use \GameDash\Sdk\FFI\Instance\Shell\Executable\Result;
    use \GameDash\Sdk\FFI\Instance\Shell\Parameter;
    use \GameDash\Sdk\FFI\Instance\FileSystem\Path;

    class SteamWorkshopDownload extends Implementation\Instance\Shell\Executable\Executable {

        /** @var Instance\Instance */
        private $Instance;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Instance = Instance\Instances::get(

                $Gateway->getParameters()->get('instance.id')->getValue()

            );

        }

        public function getDescription(): string {

            return 'Download a mod from the steam workshop';

        }

        public function execute( array $parameters ): ?Result {

            if( !isset( $parameters['id'] ) ) {

                throw new \Exception('Workshop id must be set');

            }

            if( !isset( $parameters['path'] ) ) {

                throw new \Exception('Path must be set');

            }

            $id = $parameters['id'];

            $File = $this->Instance->getFileSystem()->getFiles()->get(

                new Path\Path( $this->Instance, $parameters['path'] )

            );

            $ApiQueryResult = $this->queryApi( $id );

            if( $ApiQueryResult->getStatusCode() !== 200 ) {

                if( $ApiQueryResult->getStatusCode() === 404 ) {

                    throw new \Exception('Workshop item with id "' . $id . '" does not exist');

                }
                else {

                    throw new \Exception( $ApiQueryResult->getAsJson()['message'] );

                }

            }

            $File->downloadFrom(

                Uri::fromString( $ApiQueryResult->getAsJson()['file_url'] )

            );

            $Result = new Result();

            $Result->setValue('Successfully downloaded ' . $ApiQueryResult->getAsJson()['title']);

            return $Result;

        }

        public function getParameters(): array {

            $Id = new Parameter\Available('id');

            $Id->setIsRequired( true );

            $Path = new Parameter\Available('path');

            $Path->setIsRequired( true );

            return [

                $Id, $Path

            ];

        }

        private function queryApi( string $id ): Http\Client\Response {

            $Query = Http\Client\Client::createRequest(

                HttpMethodsEnum::get(),
                Uri::fromString('http://steamworkshopdownloader.com/api/workshop/' . $id)

            );

            $Query->send();

            return $Query->getResponse();

        }

    }

?>
