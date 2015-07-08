<?php
/**
 * Profile field menu selection where key/value pairs are pulled from an SQL statement
 *
 * Field class definition.
 *
 * @package    profilefield_menusql
 * @author     Shane Elliott, Pukunui <shane@pukunui.com>
 * @copyright  2015 onwards Pukunui {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/user/profile/field/menusql/locallib.php');

/**
 * Class profile_field_menusql
 *
 * @package    profilefield_menusql
 * @author     Shane Elliott, Pukunui <shane@pukunui.com>
 * @copyright  2015 onwards Pukunui {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_menusql extends profile_field_base {

    /** @var array $options */
    public $options;

    /**
     * Constructor method.
     *
     * Pulls out the options for the menu from the database and sets the the corresponding key for the data if it exists.
     *
     * @uses $CFG
     * @param int $fieldid
     * @param int $userid
     */
    public function profile_field_menusql($fieldid = 0, $userid = 0) {
        global $CFG;

        // First call parent constructor.
        $this->profile_field_base($fieldid, $userid);

        if (!empty($this->field->param1)) {
            $config = unserialize($this->field->param1);
        }
        if (!empty($config->dbhost)) {
            $this->options = profilefield_menusql_get_options($config);
        } else {
            if (!empty($config->dbsql)) {
                $CFG->dbsql = $config->dbsql;
            } else {
                $CFG->dbsql = '';
            }
            $this->options = profilefield_menusql_get_options($CFG);
        }

        if (empty($this->field->required)) {
            $this->options = array(0 => get_string('choose').'...') + $this->options;
        }
    }

    /**
     * Create the code snippet for this field instance
     * Overwrites the base class method
     *
     * @param moodleform $mform Moodle form instance
     */
    public function edit_field_add($mform) {
        $mform->addElement('select', $this->inputname, format_string($this->field->name), $this->options);
    }

    /**
     * The data from the form returns the key.
     *
     * @param mixed $data The key returned from the select input in the form
     * @param stdClass $datarecord The object that will be used to save the record
     * @return mixed Data or null
     */
    public function edit_save_data_preprocess($data, $datarecord) {
        // Specific conversion for null data.
        if ($data === null) {
            $data = 0;
        }
        return $data;
    }

    /**
     * HardFreeze the field if locked.
     * @param moodleform $mform instance of the moodleform class
     */
    public function edit_field_set_locked($mform) {
        if (!$mform->elementExists($this->inputname)) {
            return;
        }
        if ($this->is_locked() and !has_capability('moodle/user:update', context_system::instance())) {
            $mform->hardFreeze($this->inputname);
            $mform->setConstant($this->inputname, format_string($this->data));
        }
    }

    /**
     * Display the data for this field.
     *
     * Overrides the base class.
     *
     * @return string
     */
    public function display_data() {
        return $this->options[$this->data];
    }

    /**
     * Convert external data (csv file) from value to key for processing later by edit_save_data_preprocess
     *
     * @param string $value one of the values in menu options.
     * @return int options key for the menu
     */
    public function convert_external_data($value) {
        if (isset($this->options[$value])) {
            $retval = $value;
        } else {
            $retval = array_search($value, $this->options);
        }

        // If value is not found in options then return null, so that it can be handled
        // later by edit_save_data_preprocess.
        if ($retval === false) {
            $retval = null;
        }
        return $retval;
    }
}

