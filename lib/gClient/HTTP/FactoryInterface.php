<?php
namespace gClient\HTTP;

/**
 * Factory class for creating client and response instances
 */
interface FactoryInterface {
    /**
     * @param string URL to make HTTP request to
     * @return ClientInterface
     */
    public function makeClient($url);

    /**
     * @param mixed Handle/Data from client to pass to response
     * @return ResponseInterface
     */
    public function makeResponse($handle);
}