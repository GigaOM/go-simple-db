<?php

class GO_Simple_DB
{
	/**
	 * Setup and return an AWS SimpleDB Object, act as a singleton, by domain
	 * @return SimpleDB object
	 */
	public function __construct( $aws_sdb_domain, $aws_access_key = '', $aws_secret_key = '' )
	{
		$db = array();

		if ( ! isset( $db[ $aws_sdb_domain ] ) || ! is_object( $db[ $aws_sdb_domain ] ) )
		{
			if ( empty( $aws_access_key ) || empty( $aws_secret_key ) )
			{
				return FALSE;
			} // end if

			// @TODO: remove this check when all plugins have been ported over and we no longer need to test in old theme
			if ( ! class_exists( 'SimpleDB' ) )
			{
				include_once __DIR__ . '/external/php_sdb2/SimpleDB.php';
			}//end if

			$db[ $aws_sdb_domain ] = new SimpleDB( $aws_access_key, $aws_secret_key );

			$this->check_domain( $aws_sdb_domain );
		} // end if

		return $db[ $aws_sdb_domain ];
	} // end __construct
	
	/**
	 * Check if the SimpleDB domain exists, if not, create it
	 */
	public function check_domain( $aws_sdb_domain )
	{
		$domains = $this->get( $aws_sdb_domain )->listDomains();
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
			$this->get( $aws_sdb_domain )->createDomain( $aws_sdb_domain );
		} // end if
	} // end check_domain
}// end class

function go_simple_db( $aws_sdb_domain, $aws_access_key = '', $aws_secret_key = '' )
{
	global $go_simple_db;

	if ( ! is_object( $go_simple_db ) )
	{
		$go_simple_db = new GO_Simple_DB( $aws_sdb_domain, $aws_access_key = '', $aws_secret_key = '' );
	}// end if

	return $go_simple_db;
}// end go_simple_db