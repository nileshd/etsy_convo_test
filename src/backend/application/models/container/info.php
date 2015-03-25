<?php

/**
 * Super Class for all Info Containers
 * @package container
 * @author Nilesh Dosooye <nilesh@dosooye.com>
*/

abstract class Info
{
	public $shard;
	public $client_used;


	function __construct()
	{

	}


	public function toArray()
	{
		$array = array();
		$className = get_class($this);
		$varArray = get_class_vars($className);
		$vars = array_keys($varArray);

		for ($a=0;$a<count($vars);$a++)
		{
			$thisVar = $vars[$a];
			$array[$className][$thisVar] = $this->$thisVar;
		}
		return $array;
	}


	public function toXML()
	{
		$xml = "";
		$className = get_class($this);
		$varArray = get_class_vars($className);
		$vars = array_keys($varArray);
		$xml .= "<{$className}>\n";
		for ($a=0;$a<count($vars);$a++)
		{
			$thisVar = $vars[$a];
			$xml .= "<{$thisVar}><![CDATA[".$this->$thisVar."]]></{$thisVar}>\n";
		}
		$xml .= "</{$className}>\n";
		return $xml;
	}


	public function toJSON()
	{
		return json_encode($this);
	}




	public function addSlashesToMe()
	{

		$className = get_class($this);
		$classVars = get_class_vars($className);
		$vars = array_keys($classVars);
		for ($a=0;$a<count($vars);$a++)
		{
			$varName = $vars[$a];

			if (is_string($this->{$varName}))
			{
				$this->{$varName} = addslashes($this->{$varName});
			}
		}

	}

	public function urlDecodeMe()
	{

		$className = get_class($this);
		$classVars = get_class_vars($className);
		$vars = array_keys($classVars);
		for ($a=0;$a<count($vars);$a++)
		{
		$varName = $vars[$a];

		if (is_string($this->{$varName}))
		{
		$this->{$varName} = urldecode($this->{$varName});
		}
		}

		}



	public function mysqlEscapeMe($dbConn)
	{

		$className = get_class($this);
		$classVars = get_class_vars($className);
		$vars = array_keys($classVars);
		for ($a=0;$a<count($vars);$a++)
		{
			$varName = $vars[$a];

			if (is_string($this->{$varName}))
			{
				$this->{$varName} = mysql_real_escape_string($this->{$varName},$dbConn);
			}
		}
	}


	public function generateMapperCode()
	{

		$className = get_class($this);
		$classVars = get_class_vars($className);
		$vars = array_keys($classVars);
		$code = "";
		for ($a=0;$a<count($vars);$a++)
		{
			$varName = $vars[$a];
			$code .= "\${$className}->{$varName} = \$row['{$varName}'];\n";
		}

		return $code;
	}

	public function generateUpdateCode()
	{

		$className = get_class($this);

		$classVars = get_class_vars($className);
		$vars = array_keys($classVars);
		$code = "";
		for ($a=0;$a<count($vars);$a++)
		{
			$varName = $vars[$a];
			//$code .= "{$varName} = '{\{$className}->{$varName}',";
		}

		return $code;
	}




}


?>