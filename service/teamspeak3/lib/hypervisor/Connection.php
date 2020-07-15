<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor;

    use \GameDash\Sdk\FFI\Infrastructure\Node;

    class Connection {

        private $Node;
        private $Instance;

        public function __construct( Node\Node $Node, \TeamSpeak3_Node_Host $Instance ) {

            $this->Node = $Node;
            $this->Instance = $Instance;

        }

        public function getNode(): Node\Node {

            return $this->Node;

        }

        public function getInstance(): \TeamSpeak3_Node_Host  {

            return $this->Instance;

        }

    }

?>
