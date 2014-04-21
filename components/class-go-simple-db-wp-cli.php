<?php
/**
 * Gigaom go-simple-db's WP CLI functions
 */
class GO_Simple_DB_WP_CLI extends WP_CLI_Command
{
	private $config = NULL;

	/**
	 * lazily read in our config file.
	 */
	private function config()
	{
		if ( ! empty( $this->config ) )
		{
			return $this->config;
		}

		$this->config = apply_filters( 'go_config', array(), 'go-simple-db' );

		return $this->config;
	}//END config

	/**
	 * copy all data from one SimpleDB domain to another. this command
	 * is designed for go-mcsync-reports data but may work for other
	 * data as well.
	 *
	 * ## OPTIONS
	 *
	 * --from=<from-domain>
	 * : the aws simpledb domain to copy data from
	 * --to=<to-domain>
	 * : the aws simpledb domain to copy data to
	 * [--url=<url>]
	 * : The url of the site you want posts from.
	 * [--path=<path>]
	 * : Path to WordPress files.
	 *
	 * ## EXAMPLES
	 *
	 * wp go_simple_db refresh_data --url=accounts.gigaom.com
	 *
	 * @synopsis [--url=<url>] [--path=<path>] --from=<from-domain> --to=<to-domain> [--from-date=<from-date>]
	 */
	public function copy_domains( $unused_args, $assoc_args )
	{
		if ( $assoc_args['from'] == $assoc_args['to'] )
		{
			WP_CLI::error( 'from and to domain names must not be the same' );
		}

		// we'll refer to these several times later
		$from_domain = $assoc_args['from'];
		$to_domain = $assoc_args['to'];

		// make sure go-simple-db is accessible
		if ( ! function_exists( 'go_simple_db' ) )
		{
			WP_CLI::error( 'go-simple-db not activated' );
		}

		$config = $this->config();

		if ( ! isset( $config['aws_access_key'] ) )
		{
			WP_CLI::error( 'missing aws access key in config file' );
		}

		if ( ! isset( $config['aws_secret_key'] ) )
		{
			WP_CLI::error( 'missing aws secret key in config file' );
		}

		$from_db = go_simple_db( $from_domain, $config['aws_access_key'], $config['aws_secret_key'] );
		$to_db = go_simple_db( $to_domain, $config['aws_access_key'], $config['aws_secret_key'] );

		WP_CLI::line( 'copying data from ' . $from_domain . ' to ' . $to_domain );

		$query = 'SELECT * FROM `' . $from_domain . '`';

		$results = $from_db->select( $query, NULL, FALSE, TRUE );
		$total = count( $results );
		WP_CLI::line( 'read ' . $total . ' items from ' . $from_domain );
		if ( FALSE === $results )
		{
			WP_CLI::error( 'error reading from ' . $from_domain . ': ' . var_export( $from_db->lastError, 1 ) );
		}

		$n = 0;
		foreach ( $results as $k => $row )
		{
			if ( ! isset( $row['key'] ) || empty( $row['key'] ) )
			{
				WP_CLI::line( 'missing key value for element ' . $k );
				continue;
			}

			// convert each value to an array with a single 'value' element
			$data = array();
			foreach ( $row as $name => $value )
			{
				$data[ $name ] = array( 'value' => $value );
			}//END foreach

			if ( FALSE === $to_db->putAttributes( $to_domain, $row['key'], $data, NULL, TRUE ) )
			{
				WP_CLI::error( 'error from putAttributes() for ' . $row['key'] . ': ' . var_export( $from_db->lastError, 1 ) );
			}

			++$n;
			$status_message = $n . '/' . $total  . ' saved ' . $row['key'] . ' data';
			if ( isset( $row['nocampaign'] ) )
			{
				$status_message .= ' (no campaign)';
			}

			WP_CLI::line( $status_message );
		}//END foreach
	}//END copy_domains
}//END class

WP_CLI::add_command( 'go_simple_db', 'GO_Simple_DB_WP_CLI' );