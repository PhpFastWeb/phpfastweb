<?php

echo "Server Name: " . gethostbyaddr (gethostbyname ($_SERVER["SERVER_NAME"]));
echo "<br />";
phpinfo();

?>