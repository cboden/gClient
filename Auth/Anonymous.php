<?php
namespace cB\gData\Auth;

class Anonymous extends Adapter {
    public function getHeaderString() {
        return '';
    }
}
?>