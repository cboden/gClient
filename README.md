#gClient (Calendar)

A fully PSR-0 compliant, RESTful PHP client for Google Calendar API 2.0
The goal is to make this API intuitive without requiring knowledge of the gData API

If Calendar works out more Apps API clients can be created

##Quick Username/Password test
(a better test coming soon, Calendar\Service is about to get overhauled)

    <?php
        // Assuming gClient is in autoload path
        try {
            $conn = new gClient\Auth\ClientLogin('myaccount@gmail.com', 'my password here', 'testing-name-1a');
            $calendar = $conn->addService('Calendar')->authenticate()->getService('Calendar');

            // Get the Country code setting from your Calendar
            echo $calendar->settings->country;
        } catch (Exception $e) {
            echo $e->getMessage();
        }