<?php
/*
(shortcuts)
SimplePO::installer();
SimplePO::installer(true); //force
SimplePO::import($name,$file);
SimplePO::export($name,$file);
SimplePO_JSON_RPC::Service
*/
class SimplePO{
  protected $force;
  protected $infile;
  protected $outfile;
  protected $update = false;
  protected $preout = false;
  protected $catalogue_name;
	protected $doInstall;
	protected $stderrh;
	public static function installer($force=false){
		if($force) return SimplePO::exec('--install','--force');
		return SimplePO::exec('--install');
	}
	public static function import($name,$file){
		return SimplePO::exec('-n',$name,'-i',$file);
	}
	public static function update_db($name,$file){
		return SimplePO::exec('-n',$name,'-i',$file,'-u');
	}

	public static function export($name,$file){
		return SimplePO::exec('-n',$name,'-o',$file);
	}
	
	public static function exec(){
		$args = func_get_args();
		$s = new SimplePO();
		array_unshift($args,null);
		return $s->main(count($args), $args);
	}
	function main($argc, $argv){
	  $this->stderrh = fopen('php://stderr','w');
	  $this->parseArguments($argc, $argv);
	  $MsgStore = new SimplePO_DBPoMsgStore();	
		if($this->doInstall) {
			$this->install($this->force);
			return;
		}
	  
		if ( $this->catalogue_name ){
			$MsgStore->init( $this->catalogue_name );
		}
	  
		if ( $this->infile ){
			if ( !$this->catalogue_name ) die("Please provide a catalogue name\n");
			$MsgStore->update = $this->update;
			$POParser = new POParser($MsgStore);
			$POParser->parseEntriesFromStream(fopen( $this->infile, 'r'));
			$this->echo_stderr(sprintf("%s parsed and saved\n",$this->infile));
			return;
		}
	  
		if ( $this->outfile ) {
			if ( !$this->catalogue_name ) die("Please provide a catalogue name\n");
			$POParser = new POParser($MsgStore);
			$POParser->writePoFileToStream(fopen($this->outfile,'w'),$this->preout);
			$this->echo_stderr("$this->outfile successfully written\n\n");
			return;
		  }
		$this->usage();
	}

	function parseArguments($argc, $argv){
	  $flags = array(
	   "version" => array("-v","--version"),
	   "install" =>array("--install"),
	   "force" => array("-f", "--force")
	  );

	  $options = 	array(
	    "update" => array("-u","--update"),
	    "inputfile" => array("-i","--inputfile"),
	    "outputfile" => array("-o","--outputfile"),
	    "preout" => array("-p","--preout"),
	    "catalogue_name" => array("-n","--name")
	  );

	  $installCmd = false;
	  for($i=1; $i < count($argv); $i++) {
	      $a = $argv[$i];
	      if(in_array($a,$flags['version'])) {
	        $this->usage();
	        return;
	      }
				if ( in_array($a, $flags['force']) ){
	        $this->force = true;
	      }
	      if( in_array($a, $flags['install']) ){
	        $this->doInstall = true;
	      }
	      if ( in_array($a, $options['update']) ){
			$this->update = true;
		  }
	      if ( in_array($a, $options['preout']) && isset($argv[$i+1])){
	        $this->preout = $argv[$i+1];
		  }
	      if ( in_array($a, $options['inputfile']) ){
	        $this->infile = $argv[$i+1] or die("Please provide input filename.\n");
					if($this->infile == '-')
						$this->infile = 'php://stdin';
	      }
	      if ( in_array($a, $options['outputfile']) ){
	        $this->outfile = $argv[$i+1] or die("Please provide output filename.\n");
					if($this->outfile == '-')
						$this->outfile = 'php://stdout';
	      }
	      if ( in_array($a, $options['catalogue_name']) ){
	        $this->catalogue_name = $argv[$i+1] or die("Please provide catalogue name.\n");
	      }
	    }
	}

	function usage() {
		
		$usage = <<<USAGE
________________________________________
                SimplePO
________________________________________
Flags:
  version:  -v  --version
  install:      --install
  force:    -f  --force
Options:
  -i inputfilename
  -o outputfilename
  -n cataloguename

This is how you use this program:
To install:
  php simplepo.php --install
  or
  php simplepo.php --force --install
To read in a PO file:
  php simplepo.php -n "CatalogueName" -i inputfilename
To write to a PO file:
  php simplepo.php -n "CatalogueName" -o outputfilename

USAGE;
	  $this->echo_stderr($usage);
	}
	function echo_stderr($data) {
		fwrite($this->stderrh,$data);
	}
	function install( $force ){
	  $create_message =<<<CM
	    CREATE TABLE IF NOT EXISTS `{messages}` (
	      `id` int(11) NOT NULL auto_increment,
	      `catalogue_id` int(11) NOT NULL,
	      `msgid` text  CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	      `msgstr` text  CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	      `comments` text  CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	      `extracted_comments` text  CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	      `reference` text  CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	      `flags` text  CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	      `is_obsolete` tinyint(1) NOT NULL,
				`is_header` tinyint(1) NOT NULL,
	      `previous_untranslated_string` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	      `updated_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	      PRIMARY KEY  (`id`)
	    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
CM;

	  $create_catalogue = <<<CM
	    CREATE TABLE IF NOT EXISTS `{catalogues}` (
	      `id` int(11) NOT NULL auto_increment,
	      `name` varchar(100) NOT NULL,
	      PRIMARY KEY  (`id`),
	      UNIQUE KEY `name` (`name`)
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
CM;

	  $q = new SimplePO_Query();
	  if ($this->force) {
	    $this->echo_stderr("\tForced Installation taking place...\n");
	    $q->sql("DROP TABLE IF EXISTS {catalogues}, {messages}")->execute();
	  }
	  $q->sql($create_catalogue)->execute();
	  $q->sql($create_message)->execute();
	  $this->echo_stderr("\tInstallation complete!\n\n");
	}

}
?>