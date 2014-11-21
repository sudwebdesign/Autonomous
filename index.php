<?php
if(!@include(__DIR__.'/surikat/control.php'))
	symlink('../surikat','surikat')&&include('surikat/control.php');
dev::level(dev::STD);
view::getInstance()->index();