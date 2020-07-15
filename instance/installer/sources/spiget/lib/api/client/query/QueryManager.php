<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Spiget\Lib\Api\Client\Query;

    use \Electrum\Database;
    use \Electrum\Json\Json;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Spiget\Lib\Api\Client\Client as ApiClient;

    class QueryManager {

        /** @var Gateway\Gateway */
        private $Gateway;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

        }

        public function getAuthor( string $id ): array {

            if( !$this->Gateway->getModule()->getDataCacheManager()->exists('author.' . $id) ) {

                $Request = ApiClient::createRequest(HttpMethodsEnum::get(), 'authors/' . $id);

                $Request->send();

                $result = $Request->getResponse()->getAsJson();

                $this->Gateway->getModule()->getDataCacheManager()->set('author.' . $id, Json::encode($result));

            }

            return Json::decode(

                $this->Gateway->getModule()->getDataCacheManager()->get('author.' . $id)

            );

        }

        public function getResource( string $id ): array {

            if( !$this->Gateway->getModule()->getDataCacheManager()->exists('resource.' . $id) ) {

                $Request = ApiClient::createRequest( HttpMethodsEnum::get(), 'resources/' . $id );

                $Request->getParameters()->get('fields')->setValue('name,description,version,versions,tag,downloads,author,category,releaseDate,icon,updateDate');

                $Request->send();

                $result = $Request->getResponse()->getAsJson();

                $this->Gateway->getModule()->getDataCacheManager()->set(

                    'resource.' . $id,
                    Json::encode( $result ),
                    [

                        'expire' => 1800

                    ]

                );

            }

            return Json::decode(

                $this->Gateway->getModule()->getDataCacheManager()->get('resource.' . $id)

            );

        }

        public function resourceExists( string $id ): bool {

            if( $this->Gateway->getModule()->getDataCacheManager()->exists('resource.' . $id) ) {

                return true;

            }

            $Request = ApiClient::createRequest( HttpMethodsEnum::get(), 'resources/' . $id );

            $Request->getParameters()->get('fields')->setValue('id');

            $Request->send();

            return $Request->getResponse()->getStatusCode() === 200;

        }

    }

?>
