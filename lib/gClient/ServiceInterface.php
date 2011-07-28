<?php
namespace gClient;

/**
 * Any Service to communicate with a Google App must implement this interface
 */
interface ServiceInterface {
    /**
     * @param Connection Active connection to make requests through
     */
    public function __construct(Connection $connection);

    /**
     * Create an HTTP request class - should extend \gClient\Connection::prepareCall()
     * @param string The URL to request
     * @throws \RuntimeException If class $this->client does not implement \gClient\HTTP\ClientInterface
     * @throws \gClient\HTTP\Exception If the server returns a status code of 300 or greater
     * @throws \UnexpectedValueException If an invalid HTTP Method was set
     * @return \gClient\HTTP\ResponseInterface Instance of previously set requestor class
     */
    public function prepareCall($url);

    /**
     * @see \gClient\Auth\ClientLogin
     * @return string
     */
    public static function getClientLoginService();

    /**
     * @see \gClient\Auth\OAuth
     * @return string
     */
    public static function getOAuthScope();
}