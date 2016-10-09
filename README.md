SQL select query to mongo.
--------------

Before use, you should do the following steps: 
1) Install MongoDB driver - http://php.net/manual/en/mongo.installation.php
2) Run "composer install", for installing dependencies and configuration autoloading
3) Configure database connection parameters. Config file is located here  - src/Config/Config.php

Run application:
--------------

Open a terminal window, go to project folder and run following command:

$ php mongo

After that you can enter SELECT query.

The query must be like this:

SELECT firstname, lastname, email FROM users WHERE firstname = 'Volodymyr' OR firstname = '111111' GROUP BY none ORDER BY email ASC SKIP 2 LIMIT 10

Run test:
--------------
Open a terminal window, go to project folder and run following command:

$ phpunit


