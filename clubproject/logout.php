<?php
require 'config.php';
session_unset();
session_destroy();
$redirect_to = $_GET['redirect'] ?? 'index.php';
redirect($redirect_to);
