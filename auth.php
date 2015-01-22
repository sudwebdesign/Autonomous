<?php
if(!@include(__DIR__.'/Surikat/Loader.php'))
	symlink('../Surikat','Surikat')&&include('Surikat/Loader.php');
use Core\Dev;
Dev::on(Dev::PHP);
Dev::on(Dev::DB);
Dev::on(Dev::MODEL);
Dev::on(Dev::SQL);

use Surikat\Tool\Auth\Auth;
use Surikat\I18n\Lang;
Lang::set('fr');
$auth = new Auth();

Auth::loginCall();

$email = 'jo@surikat.pro';
$username = 'JoSurikat';
$password = $repeatpassword = 'p@88w0rd';
$newpass = $repeatnewpass = 'p@ssw0rd';
//$key = '6dn61zP11UXS64I8W9uQ';
var_dump(
	//$auth->register($email, $username, $password, $repeatpassword)
	//$auth->activate($key)
	//$auth->resendActivation($email)
	$auth->getMessage(
	$auth->login($username, $password)
	)
);
//$auth->requestReset($email)
//$auth->resetPass($key, $password, $repeatpassword)
//$auth->changePassword($uid, $currpass, $newpass, $repeatnewpass)
//$auth->changeEmail($uid, $email, $password)
//$auth->deleteUser($uid, $password)
//$auth->logout()