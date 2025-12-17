<?php

$_server = "localhost";
$_user = "root";
$_password = "12345678";
$_name = "charith";
$conn = "";

try{
    $conn = mysqli_connect($_server, $_user, $_password, $_name);
}
catch(mysqli_sql_exception){
    echo "Could not connect";
}

?>