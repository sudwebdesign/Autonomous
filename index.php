<?php include('surikat/control.php');
	control::dev(
		control::dev_default
		|control::dev_js
	);
view::index();
