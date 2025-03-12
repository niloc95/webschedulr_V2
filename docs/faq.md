# FAQ

## How do I check that my server has Apache, Php, and MySQL already installed?

@webSchedulr is a PHP application and requires an Apache server with PHP and MySQL. The PHP "curl" extension and the Apache "mod_rewrite" module must also be enabled. To check, create a PHP file in your web root with the content `<?php phpinfo(); ?>` and access it via the browser (e.g., "http://domain-name.com/phpinfo.php"). This URL will display server details.

## How do I create a Google Calendar API key?

To integrate Google Calendar with @webSchedulr, create an API key using a Google account. After logging in, go to the Google Developers Console, create a new project, enable the Calendar API, and generate an OAuth client ID. Enter the correct redirect URL in the process: "http://domain-name/folder-to-webschedulr-installation/google/oauth_callback" (replace with your actual domain and installation path).

## Installation Page Is Not Working

If the installation page isn’t working, it could be due to either an incorrect `configuration.php` file or server settings preventing AJAX calls. Verify the `$base_url` is set correctly in `configuration.php`, and ensure your database credentials are correct. If the issue persists, review the `.htaccess` file and check the Apache error logs.

**.htaccess:**


## Booking Wizard Won't Display Any Hours

If no hours are showing in the booking wizard, check the Apache error log and the browser's JavaScript console for server-side issues. Contact your hosting provider to resolve these server-related issues.

## Booking Wizard Displays "There are no available appointment hours for the selected date."

This issue typically arises from default settings in the clean installation. The default working plan may have breaks, preventing longer services from fitting into available slots. Adjust the working plan or break times for providers to fix this.

## Installing @webSchedulr on Subdomain Won't Load Appointment Hours

If installing @webSchedulr on a subdomain, make sure the `$base_url` in `configuration.php` points to the subdomain URL. For example, use `$base_url = 'http://book.mysite.com'` if the subdomain is "book.mysite.com" rather than the main domain's directory.

## Change the gap of the available hours of the booking wizard to 60 minutes.

To change the default 15-minute gap to 60 minutes, use a provided script available on the forum. For more details, check [this thread](https://groups.google.com/d/msg/easy-appointments/Mdt98fbF8hE/9CEjOvW7FAAJ).

## DateTime::__construct(): It is not safe to rely on the system's timezone settings...

To resolve the "timezone" warning, ensure that your PHP installation has the correct timezone set in the `php.ini` file. If you cannot modify the `php.ini`, set the timezone manually in `index.php` with `date_default_timezone_set('America/Los_Angeles');` (use your own timezone).

*This document applies to @webSchedulr v1.5.1.*

[Back](readme.md)
