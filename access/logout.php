<?php
session_start();
session_destroy();
header("Location: ../access/login/login.php");
exit();
