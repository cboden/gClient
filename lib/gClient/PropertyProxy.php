<?php
namespace gClient;

class PropertyProxy {
    protected $readonly = Array();

    protected function setData(Array $data) {
        $this->readonly = $data + $this->readonly;
    }

    public function &__get($name) {
        if (!isset($this->readonly[$name])) {
            $this->readonly[$name] = '';
        }

        return $this->readonly[$name];
    }
}