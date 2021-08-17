<?php

namespace mod_evokeportfolio\event;

/**
 * The course_module_viewed event class.
 *
 * @package     mod_evokeportfolio
 * @category    event
 * @copyright   2021 onwards World Bank Group
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class course_module_viewed extends \core\event\course_module_viewed {
    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'evokeportfolio';
    }

    public static function get_objectid_mapping() {
        return array('db' => 'evokeportfolio', 'restore' => 'evokeportfolio');
    }
}
