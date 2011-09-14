#gClient (Calendar)

A fully PSR-0 compliant, RESTful PHP client for Google Calendar API 2.0.  
The goal is to make this API intuitive without requiring knowledge of the gData API.  

Note: It is strongly recommended using OAuth2 for authentication.  
If you choose to use the ClientLogin instead, please be aware that often when using the ClientLogin for the first time Google will issue a CAPTCHA challenge.  
If a CAPTCHA challenge is issued gClient throws an Exception.  Currently you must go to your browser, login as that account, accepting the CAPTCHA.  
Once you have passed the CAPTCHA (externally) your application will connect.

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
* Sharing
* Implement Iterator Interface on meta/settings classes

---

##Quick ClientLogin: List your calendars

    <?php
        // Assuming gClient is in autoload path
        try {
            $conn = new gClient\Auth\ClientLogin('myaccount@gmail.com', 'my password here', 'testing-name-1a');
            $calendars = $conn->addService('Calendar')->authenticate()->getService('Calendar');

            foreach ($calendars as $calendar) {
                echo "{$calendar->settings->title}\n";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }