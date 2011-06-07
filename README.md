#gClient (Calendar)

A fully PSR-0 compliant, RESTful PHP client for Google Calendar API 2.0.  
The goal is to make this API intuitive without requiring knowledge of the gData API.  

If Calendar works out more Apps API clients can be created (Contacts, URL Shortener, etc.)

---

### Completed functionality
* Abstract HTTP client interface
* cURL HTTP client implementation
* ClientLogin Authentication (username/password)
* OAuth2 Authentication
* Get user's Google Calendar Application settings
* Retreive calendars
* Create/Delete/Subscribe/Unsubscribe calendars
* Read/write a calendar's properties
* Query raw event data

### ToDo
* Convert received string data to native data types
* Unify object creation
* Plan/write Event objects and interaction between Calendars
* Better and more unit testing
* Consider simplifying Service interactions (refactor create/subscribe, delete/unsubscribe)
* Sort Service calendars to match server (owned, subscriptions, alphabetical)
* Better exceptions

---

##Quick ClientLogin test

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