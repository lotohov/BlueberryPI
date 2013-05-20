<?php
/**
 * Main Config class for our Application
 */
class Config {

	protected $dbh;


	function __construct($_dbh) {
		
		$this->dbh = $_dbh;
		//Get the config values from the database
		$this->getConfig();
	}

	/**
	 * Populate the object with the configuration values from the database. Config values will be saved in public
	 * Property of this object
	 * @return bool
	 */
	function getConfig() {
		//Get all the config values from the database
		//Every row in the database is a config value, items with the same name are array config values
		$sql = "SELECT * from config";

		//Create a config array. For items that have a single entry in the config table they are string values
		//Multiple items of a config name turn into an array
		foreach( $this->dbh->query($sql) as $myrow ) {

			if(!isset($this->values[$myrow['name']])) {
				$this->values[$myrow['name']] = $myrow['value'];
			} else {

				if(!is_array($this->values[$myrow['name']])) {
					$firstvalue = $this->values[$myrow['name']];
					unset($this->values[$myrow['name']]);
					$this->values[$myrow['name']][] = $firstvalue;
					$this->values[$myrow['name']][] = $myrow['value'];
				} else {
					$this->values[$myrow['name']][] = $myrow['value'];		
				} 
			}
		}


		$this->original_config = $this->values;

		//if the API key entry is empty we need to randomly genereate one!

		if(empty($this->values['apikey'])) {

			$this->values['apikey'] = md5(time());

		}
	}

	/**
	 * Save this object's config values back to the database. The database will be locked so we don't have 
	 * race conditions of different procceses writing out the config
	 * @param  PDOObject $dbh
	 * @return bool
	 */
	function save_config() {

		//We need to save the changes I've made the config array out to the database;

		//Find any values which need to be delete from the database
		$sql = "LOCK TABLES config WRITE";
		
		$this->dbh->query($sql);
		foreach($this->original_config as $name=>$value) {



			if(!isset($this->config->values[$name])) {
				
				$sql = "DELETE from config WHERE name = ':name'";

				$statement = $this->dbh->prepare($sql);

				$name = filter_var($name,FILTER_SANITIZE_STRING);

				$statement->bindParam(':name',$name);
				$statement->execute();
				
			} else {
				if($this->original_config[$name] != $config[$name]) {
					
					if(is_array($config[$name])) {
							
						//Delete all exists config values and replace them with the new ones;

						$sql = "DELETE from config WHERE name = ':name'";

						$statement = $this->dbh->prepare($sql);
						$name = filter_var($name,FILTER_SANITIZE_STRING);
						$statement->bindParam(":name",$name);
						$statement->execute();
						

						foreach($config[$name] as $value) {
							if(is_array($value)) {
								$value = implode(",", $value);
							}
							$sql = "INSERT INTO config VALUES('',':name',':value')";
							
							$statement = $this->dbh->prepare($sql);
							$statement->bindParam(":name",filter_var($name,FILTER_SANITIZE_STRING));
							$statement->bindParam(":value",filter_var($value,FILTER_SANITIZE_STRING));
							$statement->execute();
						}


					} else {
						if(is_array($config[$name])) {
							$config[$name] = implode(",", $config[$name]);
						}
						$sql = "UPDATE config SET value = ':newname' WHERE name = ':name'";
						$statement =$this->dbh->prepare($sql);
						$statement->bindParam(":newname",$config[$name]);
						$statement->bindParam(":name",$name);
						$statement->execute();

					}


				}
			}

		}

		foreach($this->values as $name=>$value) {
			if(!isset($this->original_config[$name])) {
				if(is_array($config[$name])) {
						
					//Delete all exists config values and replace them with the new ones;

					$sql = "DELETE from config WHERE name = ':name'";
					$statement = $this->dbh->prepare($sql);
					$statement->bindParam(":name",filter_var($name,FILTER_SANITIZE_STRING));
					$statement->execute();
					
					foreach($config[$name] as $value) {
						$sql = "INSERT INTO config VALUES('',:name,:value)";
					
						$statement = $this->dbh->prepare($sql);
						$statement->bindParam(':name',filter_var($name,FILTER_SANITIZE_STRING));
						$statement->bindParam(':value',filter_var($value,FILTER_SANITIZE_STRING));
						$statement->execute();	
					}
				} else {
					$sql = "INSERT into config VALUES('',:name,:configvalue)";
					
					$statement = $this->dbh->prepare($sql);
					$statement->bindParam(":name",filter_var($name,FILTER_SANITIZE_STRING));
					$statement->bindParam(":configvalue",$config[$name]);
					$statement->execute();
				}
		}
		}
		
		$sql = "UNLOCK TABLES";
		$this->dbh->query($sql);

	}
	
}
?>