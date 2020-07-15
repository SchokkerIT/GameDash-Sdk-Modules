<?php

    namespace GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\PrivilegeKey;

    use \Electrum\Time\Time;
    use \GameDash\Sdk\Module\Implementation\Service\TeamSpeak3\Lib\Hypervisor\VirtualServer\Group;

    class PrivilegeKey {

        private $TimeCreated;

        private $key;
        private $description;

        public function __construct( string $key, string $description, Time $TimeCreated ) {

            $this->key = $key;
            $this->description = $description;

            $this->TimeCreated = $TimeCreated;

        }

        public function getKey(): string {

            return $this->key;

        }

        public function getDescription(): string {

            return $this->description;

        }

        public function getGroup(): Group\Group {



        }

        public function getTimeCreated(): Time {

            return $this->TimeCreated;

        }

    }

?>
