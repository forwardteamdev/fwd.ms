Forward Team
============


Installation
-------------

Create schema:

    bin/console doctrine:mongodb:schema:create

Create admin user:

    bin/console fos:user:create
    
Create oAuth Client:

    bin/console app:oauth_client:create <redirectUri>


Get Auth Token:

    POST http://local.docker:3000/app_dev.php/oauth/v2/token
    Cache-Control: no-store, private
    Content-Type: application/json
    Connection: close
    
    {
        "grant_type": "password",
        "client_id": "591357d8da515400fc5047e1_2rrl953vi20w4wgwcgg0ss4sgoossw0kw0go48s80ks0s80g0w",
        "client_secret": "5p7loff3siskkw4og0swg840wsgc4wg40o4gsswowgws0c4gss",
        "username": "admin",
        "password": "123"
    }