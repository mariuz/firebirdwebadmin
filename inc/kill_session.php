<?php

require('configuration.inc.php');

session_start();
session_destroy();

header('Location: ../database.php');

?>