<?php
namespace gClient\Auth;

interface AuthenticatorInterface {
    /**
     * @returns string
     */
    public function getHeaderString();
}