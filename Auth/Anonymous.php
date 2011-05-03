<?php
namespace cB\gClient\Auth;

class Anonymous extends Adapter {
    public function getHeaderString() {
        return '';
    }
}