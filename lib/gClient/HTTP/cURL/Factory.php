<?php
namespace gClient\HTTP\cURL;
use gClient\HTTP\FactoryInterface;

/**
 * cURL Factory class for creating client and response instances
 */
class Factory implements FactoryInterface {
    /**
     * @return Client
     */
    public function makeClient($url) {
        return new Client($url, $this);
    }

    /**
     * @return Response
     */
    public function makeResponse($handle) {
        return new Response($handle);
    }
}