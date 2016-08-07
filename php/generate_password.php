<?php



$pwd = $_GET['pwd'];
echo 'password: ' . $pwd . "<br/>";
$hash = password_hash($_GET['pwd'], PASSWORD_BCRYPT, $options);
echo 'hash: ' . $hash . "<br/>";
if(password_verify($hash, password_hash($pwd, PASSWORD_BCRYPT, $options)). "<br/>") {
    echo 'Verified';
} else {
    echo 'Not verified';
}
echo var_dump(password_get_info($hash)). "<br/>";