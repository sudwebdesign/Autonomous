<?php
if(!@include(__DIR__.'/Surikat/Bootstrap.php'))
	symlink('../Surikat','Surikat')&&include('Surikat/Bootstrap.php');