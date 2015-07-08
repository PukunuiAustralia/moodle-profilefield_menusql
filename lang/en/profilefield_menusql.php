<?php
/**
 * Profile field menu selection where key/value pairs are pulled from an SQL statement
 *
 * Language string definitions, language 'en'
 *
 * @package    profilefield_menusql
 * @author     Shane Elliott, Pukunui <shane@pukunui.com>
 * @copyright  2015 onwards Pukunui {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['dbsql'] = 'SQL Statement';
$string['dbtype'] = 'DB Type';
$string['dbhost'] = 'DB Host';
$string['dbuser'] = 'DB User';
$string['dbpass'] = 'DB Pass';
$string['dbname'] = 'DB Name';
$string['defaultkey'] = 'Default key';
$string['errorconnectingtodb'] = 'No values were returned. Error in SQL or connection';
$string['menusqldescription'] = 'You can enter any SQL statement in here as long as it returns two fields which must be named "key" and "value". eg <code>SELECT id AS "key", CONCAT(firstname,\' \',lastname) AS "value" FROM mdl_user</code><br />
If you leave the DB Host field empty, the SQL query will be run on the site database ie the settings defined in your config.php file to access your site database will be used<br />
<span class="bg-danger"><strong>WARNING: No checks are done on the SQL so be VERY CAREFUL!</strong> You have been warned</span>';
$string['pluginname'] = 'SQL Menu';
