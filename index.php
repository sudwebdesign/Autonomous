<?php
if(!@include(__DIR__.'/surikat/control.php'))
	symlink('../surikat','surikat')&&include('surikat/control.php');
control::dev();
view::index();