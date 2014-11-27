<?php
if(!@include(__DIR__.'/Surikat/Loader.php'))
	symlink('../Surikat','Surikat')&&include('Surikat/Loader.php');
Dev::level(Dev::STD);
View::getInstance()->index();