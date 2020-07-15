<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarPaper\Lib\Api\Client\Query;

    use \Electrum\Database;
    use \Electrum\Json\Json;
    use \Electrum\Enums\Network\Http\Methods as HttpMethodsEnum;
    use \Electrum\Userland\Sdk\Module\Gateway;
    use \GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftJarPaper\Lib\Api\Client\Client as ApiClient;

    class QueryManager {

        /** @var Gateway\Gateway */
        private $Gateway;

        public function __construct( Gateway\Gateway $Gateway ) {

            $this->Gateway = $Gateway;

        }

        /**
         * @return string[]
         * @throws \Electrum\Json\InvalidJsonException
         * @throws \Electrum\Userland\Sdk\Module\Cache\Data\CacheManagerException
         */
        public function getVersionIds(): array {

            if( !$this->Gateway->getModule()->getDataCacheManager()->exists('versionIds') ) {

                $Request = ApiClient::createRequest(HttpMethodsEnum::get(), 'paper');

                $Request->send();

                $versions = $Request->getResponse()->getAsJson()['versions'];

                $this->Gateway->getModule()->getDataCacheManager()->set(

                    'versionIds', Json::encode( $versions ), [

                        'expire' => 1800

                    ]

                );

            }

            return Json::decode(

                $this->Gateway->getModule()->getDataCacheManager()->get('versionIds')

            );

        }

        public function getVersion( string $id ): array {

            if( !$this->Gateway->getModule()->getDataCacheManager()->exists('version.' . $id) ) {

                $Request = ApiClient::createRequest(HttpMethodsEnum::get(), 'paper/' . $id);

                $Request->send();

                $result = $Request->getResponse()->getAsJson();

                $this->Gateway->getModule()->getDataCacheManager()->set(

                    'version.' . $id, Json::encode( $result ), [

                        'expire' => 1800

                    ]

                );

            }

            return Json::decode(

                $this->Gateway->getModule()->getDataCacheManager()->get('version.' . $id)

            );

        }

    }

?>
