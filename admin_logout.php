<?php
require 'config_db.php';
session_destroy();
header('Location: index.php');
exit;
