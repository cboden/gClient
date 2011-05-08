<?php
namespace gClient\Auth;

class Anonymous extends Adapter {
    public function getHeaderString() {
        return '';
    }
}