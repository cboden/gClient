<?php
namespace gClient\Auth;

interface AuthenticatorInterface {
    /**
     * @return string
     * @internal
     */
    public function getHeaderString();
}