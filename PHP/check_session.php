<?php
session_start();
if (isset($_SESSION['role'])) {
    echo $_SESSION['role'];
} else {
    echo "no_session";
}
exit;
?>
