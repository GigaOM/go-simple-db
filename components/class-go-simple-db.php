<?php

class GO_Simple_DB
{
	public $db;

	/**
	 * Setup and return an AWS SimpleDB Object, act as a singleton, by domain
	 *
	 * @param string $aws_sbd_domain The Amazon Web Services SimpleDB domain
	 * @param string $aws_acess_key Optional, default empty string. The Amazon Web Services access key
	 * @param string $aws_secret_key Optional, default empty string. The Amazon Web Services secret key
	 * @return SimpleDB object
	 */
	public function __construct( $aws_sdb_domain, $aws_access_key = '', $aws_secret_key = '' )
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

		$this->db = new g_g\php_sdb2\SimpleDB( $aws_access_key, $aws_secret_key );

		$this->check_domain( $aws_sdb_domain );
	} // end __construct

	/**
	 * Check if the SimpleDB domain exists, if not, create it
	 *
	 * @param string $aws_sdb_domain The Amazon Web Services SimpleDB domain
	 */
	public function check_domain( $aws_sdb_domain )
	{
		// 100 is the max number of domains page size the server would
		// return. this is enforced on the server (aws) side
		$max_domains = 100;

		$domains = $this->db->listDomains( $max_domains );
		while ( ! empty( $this->db->NextToken ) )
		{
			$more_domains = $this->db->listDomains( $max_domains, $this->db->NextToken );
			if ( empty( $more_domains ) || ! $more_domains )
			{
				break;
			}
			$domains = array_merge( $domains, $more_domains );
			if ( $max_domains > count( $more_domains ) )
			{
				break; // no need to make another api call
			}
		}//END while

		if ( is_array( $domains ) )
		{
			foreach ( $domains as $domain )
			{
				if ( $domain == $aws_sdb_domain )
				{
					return; // no need to create one
				}
			}//end foreach
		}//end if

		$this->db->createDomain( $aws_sdb_domain );
	}//end check_domain
}//end class

/**
 * Singleton function
 *
 * @param string $aws_sdb_domain The Amazon Web Services SimpleDB domain
 * @param string $aws_acess_key Optional, default empty string. The Amazon Web Services access key
 * @param string $aws_secret_key Optional, default empty string. The Amazon Web Services secret key
 * @return GO_Simple_DB singleton to get a SimpleDB object
 */
function go_simple_db( $aws_sdb_domain, $aws_access_key = '', $aws_secret_key = '' )
{
	global $go_simple_db;

	if ( ! is_array( $go_simple_db ) )
	{
		$go_simple_db = array();
	} // END if

	if ( ! isset( $go_simple_db[ $aws_sdb_domain ] ) || ! is_object( $go_simple_db[ $aws_sdb_domain ] ) )
	{
		$go_simple_db[ $aws_sdb_domain ] = new GO_Simple_DB( $aws_sdb_domain, $aws_access_key, $aws_secret_key );
	}// end if

	return $go_simple_db[ $aws_sdb_domain ]->db;
}// end go_simple_db