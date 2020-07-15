<?php

    namespace GameDash\Sdk\Module\Implementation\Instance\Installer\Sources\Spiget\Resources\Category;

    use \GameDash\Sdk\Module\Base\Implementation;

    class Category extends Implementation\Instance\Installer\Source\Category\Category {

        public function __construct( string $id, string $title ) {

            parent::__construct( $id );

            $this->setTitle( $title );

        }

    }

?>
