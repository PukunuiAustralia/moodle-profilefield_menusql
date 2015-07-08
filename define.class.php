<?php
/**
 * Profile field menu selection where key/value pairs are pulled from an SQL statement
 *
 * Version information
 *
 * @package    profilefield_menusql
 * @author     Shane Elliott, Pukunui <shane@pukunui.com>
 * @copyright  2015 onwards Pukunui {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/user/profile/field/menusql/locallib.php');

/**
 * Class profile_define_menusql
 *
 * @package    profilefield_menusql
 * @author     Shane Elliott, Pukunui <shane@pukunui.com>
 * @copyright  2015 onwards Pukunui {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_define_menusql extends profile_define_base {

    /**
     * Adds elements to the form for creating/editing this type of profile field.
     *
     * @param moodleform $form
     */
    public function define_form_specific($form) {

        // Hidden element for param1.
        $form->addElement('hidden', 'param1');
        $form->setType('param1', PARAM_RAW);

        // Static help blurb text.
        $form->addElement('static', 'menusqldescription', '', get_string('menusqldescription', 'profilefield_menusql'));

        // db sql.
        $form->addElement('text', 'dbsql', get_string('dbsql', 'profilefield_menusql'), array('size' => 50));
        $form->setType('dbsql', PARAM_RAW);

        // db host. If blank then we use the moodle db.
        $form->addElement('text', 'dbhost', get_string('dbhost', 'profilefield_menusql'), array('size' => 20));
        $form->setType('dbhost', PARAM_RAW);

        // db name.
        $form->addElement('text', 'dbname', get_string('dbname', 'profilefield_menusql'), array('size' => 20));
        $form->setType('dbname', PARAM_RAW);

        // db user.
        $form->addElement('text', 'dbuser', get_string('dbuser', 'profilefield_menusql'), array('size' => 20));
        $form->setType('dbuser', PARAM_RAW);

        // db pass.
        $form->addElement('text', 'dbpass', get_string('dbpass', 'profilefield_menusql'), array('size' => 20));
        $form->setType('dbpass', PARAM_RAW);

        $options = array('mysqli' => 'mysqli', 'pgsql' => 'pgsql', 'mariadb' => 'mariadb', 'mssql' => 'mssql');
        // db type.
        $form->addElement('select', 'dbtype', get_string('dbtype', 'profilefield_menusql'), $options);
        $form->setType('dbtype', PARAM_RAW);
        $form->setDefault('dbtype', 'mysqli');

        // Default data.
        $form->addElement('text', 'defaultdata', get_string('defaultkey', 'profilefield_menusql'), 'size="5"');
        $form->setType('defaultdata', PARAM_RAW);
    }

    /**
     * Validates data for the profile field.
     *
     * @uses $CFG
     * @param array $data
     * @param array $files
     * @return array
     */
    public function define_validate_specific($data, $files) {
        global $CFG;

        $errors = array();

        // Check that we can retrieve some values.
        if (!empty($data->dbhost)) {
            $options = profilefield_menusql_get_options((object)$data);
        } else {
            $CFG->dbsql = $data->dbsql;
            $options = profilefield_menusql_get_options($CFG);
        }

        if (empty($options)) {
            $errors['dbsql'] = get_string('errorconnectingtodb', 'profilefield_menusql');
        }

        return $errors;
    }

    /**
     * Processes data before it is saved.
     * @param array|stdClass $data
     * @return array|stdClass
     */
    public function define_save_preprocess($data) {

        // Store the databae data in param1 as a serialised object.
        $config = new stdclass;
        $config->dbsql  = $data->dbsql;
        $config->dbtype = $data->dbtype;
        $config->dbhost = $data->dbhost;
        $config->dbuser = $data->dbuser;
        $config->dbpass = $data->dbpass;
        $config->dbname = $data->dbname;

        $data->param1 = serialize($config);
        unset($data->dbsql);
        unset($data->dbtype);
        unset($data->dbhost);
        unset($data->dbuser);
        unset($data->dbpass);
        unset($data->dbname);

        return $data;
    }

    /**
     * Define after data
     * Basically we want to unserialize what's stored in param1 and use it to set other values.
     *
     * @param moodleform $mform
     */
    public function define_after_data(&$mform) {
        $param1 = $mform->getElement('param1');
        $data = unserialize($param1->getValue());

        // Retrieve previously saved data.
        if (!empty($data->dbsql)) {
            $field = $mform->getElement('dbsql');
            $field->setValue($data->dbsql);
        }
        if (!empty($data->dbtype)) {
            $field = $mform->getElement('dbtype');
            $field->setValue($data->dbtype);
        }
        if (!empty($data->dbhost)) {
            $field = $mform->getElement('dbhost');
            $field->setValue($data->dbhost);
        }
        if (!empty($data->dbuser)) {
            $field = $mform->getElement('dbuser');
            $field->setValue($data->dbuser);
        }
        if (!empty($data->dbpass)) {
            $field = $mform->getElement('dbpass');
            $field->setValue($data->dbpass);
        }
        if (!empty($data->dbname)) {
            $field = $mform->getElement('dbname');
            $field->setValue($data->dbname);
        }
    }

}
