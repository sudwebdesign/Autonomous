<?php
if(!@include(__DIR__.'/Surikat/Loader.php'))
	symlink('../Surikat','Surikat')&&include('Surikat/Loader.php');
use Core\Dev;
//Dev::on(Dev::PHP);
//Dev::on(Dev::CONTROL);
//Dev::on(Dev::VIEW);
//Dev::on(Dev::PRESENT);
//Dev::on(Dev::MODEL);
//Dev::on(Dev::DB);
//Dev::on(Dev::DBSPEED);
//Dev::on(Dev::SQL);
Dev::on(Dev::ROUTE);
//Dev::on(Dev::I18N);
//Dev::on(Dev::IMG);
//Dev::on(Dev::SERVER);	//PHP+CONTROL+VIEW+PRESENT+MODEL+DB+SQL+I18N
//Dev::on(Dev::NAV);	//ROUTE+JS+CSS+IMG
Dev::on(Dev::STD);		//PHP+CONTROL+VIEW+PRESENT+MODEL+DB+I18N
Dev::on(Dev::CSS);
Dev::on(Dev::JS);

(new Dispatcher\Index())->run(@$_SERVER['PATH_INFO']);