<?php

require 'requires/session.php';

$_SESSION['msg'] = array("msg" => "You have to use a browser that supports Javascript. In case if you have manually disabled JavaScript, you have to enable it in order to move forward", "title" => "Unsupported Browser", "link" => "index.php", "legend" => "Reload Page Again");
echo "<script type=\"text/javascript\">window.location='msg.php';</script>";


?>