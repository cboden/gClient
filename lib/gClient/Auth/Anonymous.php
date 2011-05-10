<?php
namespace gClient\Auth;

/**
 * This Authentication class allows the user to make an
 * anonymous connection to get public data from Google
 */
class Anonymous extends Adapter {
    public function getHeaderString() {
        return '';
    }
}