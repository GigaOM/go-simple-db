<?php

class Go_Simple_DB
{
	public static $simple_dbs = array();
	
	/**
	 * Check if the SimpleDB domain exists, if not, create it
	 */
	public static function check_domain( $aws_sdb_domain )
	{
		$domains = static::$simple_dbs[ $aws_sdb_domain ]->listDomains();
		$exists  = FALSE;
		
		if ( $domains )
		{
			foreach ( $domains as $domain )
			{
				if ( $domain == $aws_sdb_domain ) 
				{
					$exists = TRUE;
					break;
				} // end if
			} // end foreach
		} // end if
		
		if ( $exists == FALSE )
		{
			static::$simple_dbs[ $aws_sdb_domain ]->createDomain( $aws_sdb_domain );
		} // end if
	} // end function check_domain
	
	/**
	 * Setup and return an AWS SimpleDB Object, act as a singleton, by domain
	 * @return SimpleDB object
	 */
	public static function get( $aws_sdb_domain, $aws_access_key = '', $aws_secret_key = '' )
	{
		if ( ! is_object( static::$simple_dbs[ $aws_sdb_domain ] ) )
		{
			if ( empty( $aws_access_key ) || empty( $aws_secret_key ) )
			{
				return false;
			} // end if
			
	 		require_once __DIR__ . '/external/php_sdb2/SimpleDB.php';
			
			static::$simple_dbs[ $aws_sdb_domain ] = new SimpleDB( $aws_access_key, $aws_secret_key );

			static::check_domain( $aws_sdb_domain );
		} // end if

		return static::$simple_dbs[ $aws_sdb_domain ];
	} // end function go_get_simple_db

} // end class Go_Simple_DB