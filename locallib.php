<?php
/**
 * Profile field menu selection where key/value pairs are pulled from an SQL statement
 *
 * Local library functions
 *
 * @package    profilefield_menusql
 * @author     Shane Elliott, Pukunui <shane@pukunui.com>
 * @copyright  2015 onwards Pukunui {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return an associative array of values
 *
 * @param object $config  database connection and sql
 * @return array
 */
function profilefield_menusql_get_options($config) {

    $values = array();
    $adodb = profilefield_menusql_db_init($config);

    if (!empty($adodb) and $adodb->IsConnected()) {
        if ($rs = $adodb->Execute($config->dbsql)) {
            while ($fields = $rs->FetchRow()) {
                $values[$fields['key']] = $fields['value'];
            }
            $rs->Close();
        }
        $adodb->Close();
    }
    return $values;
}

/**
 * Tries to make connection to the external database.
 *
 * @uses $CFG
 * @param object $config  database connection and sql
 * @return null|ADONewConnection
 */
function profilefield_menusql_db_init($config) {
    global $CFG;

    require_once($CFG->libdir.'/adodb/adodb.inc.php');

    // Connect to the external database (forcing new connection).
    $extdb = ADONewConnection($config->dbtype);

    // The dbtype my contain the new connection URL, so make sure we are not connected yet.
    if ($extdb) {
        if (!$extdb->IsConnected()) {
            $result = $extdb->Connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, true);
            if (!$result) {
                return null;
            }
        }
        $extdb->SetFetchMode(ADODB_FETCH_ASSOC);
        return $extdb;
    }
    return null; 
}

