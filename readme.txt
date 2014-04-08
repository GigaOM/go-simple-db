=== Gigaom Simple DB ===
Contributors: methnen, zbtirrell

Tags: wordpress, amazon simple db
Requires at least: 3.6.1
Tested up to: 3.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Establish a connection to Amazon Simple DB. Uses the awesome [php-sdb2](https://github.com/g-g/php-sdb2) SDB library.

== Description ==

Uses a combination of jQuery addons including maphighlight, Timezone Picker, and Timpicker Addon to present a nice UI for date/time and timezone selections.

=== Usage ===

1. Load simple db class
	* ```$go_simple_db = go_simple_db( 
		YOUR_SDB_DOMAIN,
		YOUR_KEY,
		YOUR_SECRET
	);```
	* The SDB Domain value is analagous to an SQL Table. If the SDB Domain doesn't exist yet Gigaom Simple DB will create it for you.
	* See: [Amazon Simple DB Getting Started Guide](http://docs.aws.amazon.com/AmazonSimpleDB/latest/GettingStartedGuide/Welcome.html) for more information on getting your Key and Secret values.
	* Also see: [SDB Limits](http://docs.aws.amazon.com/AmazonSimpleDB/latest/DeveloperGuide/SDBLimits.html) to get an idea of what Amazon Simple DB allows as it's not going to be the same as other databases you've worked with before.
2. Write a row to the your SDB Domain: ```$go_simple_db->putAttributes( YOUR_SDB_DOMAIN, array( 'post_id' => array( 'value' => '1234' ) ) )```
3. Query your SDB Domain: ```$go_simple_db->select( 'SELECT * FROM `' . YOUR_SDB_DOMAIN . '` LIMIT 100, NULL );```
	* In the case where you are getting the next chunk of results from you SDB Domain the NULL value in the code above would be replaced with a SDB Next Token (which essentially triggers an offset in the query) see the [Developer Guide](http://docs.aws.amazon.com/AmazonSimpleDB/latest/DeveloperGuide) for more info on that. 
	* Queries are quite different in SDB and you'll need to familiarize yourself with those differences.
	* Did I mention the [SDB Developer Guide](http://docs.aws.amazon.com/AmazonSimpleDB/latest/DeveloperGuide)?

== Installation ==

1. Upload `go-simple-db` to the `/wp-content/plugins/` directory
2. Activate 'Gigaom Simple DB' through the 'Plugins' menu in WordPress

== Contributing ==

This plugin is developed and [available on GitHub](https://github.com/GigaOM/go-simple-db). Contributions and questions are welcome!