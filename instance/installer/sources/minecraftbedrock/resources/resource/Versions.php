<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\MinecraftBedrock\Resources\Resource;

    use \PHPHtmlParser;
    use \Electrum\Uri\Uri;

    class Versions {

        public static function getLatestDownloadUri(): Uri {

            $Dom = new PHPHtmlParser\Dom;
            $Dom->loadFromUrl('https://www.minecraft.net/en-us/download/server/bedrock');

            foreach( $Dom->getElementsbyTag('a') as $Element ) {

                if( $Element->getAttribute('data-platform') === 'serverBedrockLinux' ) {

                    return Uri::fromString( $Element->getAttribute('href') );

                }

            }

        }

    }

?>
