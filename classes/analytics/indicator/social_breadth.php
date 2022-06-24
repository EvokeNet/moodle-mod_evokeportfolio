<?php

/**
 * Social breadth indicator - evokeportfolio.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

namespace mod_evokeportfolio\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Social breadth indicator - evokeportfolio.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class social_breadth extends activity_base {

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('indicator:socialbreadth', 'mod_evokeportfolio');
    }

    public function get_indicator_type() {
        return self::INDICATOR_SOCIAL;
    }

    public function get_social_breadth_level(\cm_info $cm) {
        $this->fill_instance_data($cm);

        return self::SOCIAL_LEVEL_2;
    }
}
