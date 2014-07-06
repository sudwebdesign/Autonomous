<?php  namespace Surikat;
Rights::lock('admin');
return FS::rec_unlink(SURIKAT_TMP,false);
?>