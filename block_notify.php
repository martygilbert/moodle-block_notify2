<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Newblock block caps.
 *
 * @package    block_notify
 * @copyright  Daniel Neis <danielneis@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_notify extends block_base {

    function init() {
        //$this->title = get_string('pluginname', 'block_notify');
        $this->title = get_string('blockdispname', 'block_notify');
    }

    function get_content() {
        global $CFG, $OUTPUT, $USER, $COURSE, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        //error_log($USER->id."\t".$COURSE->id);

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';


        $messages = $DB->get_records('block_notify', array('mdluserid'=>$USER->id, 'courseid'=>$COURSE->id));
        
        if(!$messages) return;

        $now = time();
        $numMessages = 0;
        $this->content->text = ' ';
        foreach ($messages as $msg) {

            if($now > $msg->end || $now < $msg->start) continue;

            $numMessages++;
            $this->content->text .= '<h2>'.$msg->title.'</h2>'."\n";
            $this->content->text .= $msg->message;
            $this->content->text .= "\n".'<hr style="border-top: 3px double" />';
        }

        if($numMessages == 0) {
            $this->content->text = '';
            return;
        }
        
        //strip the last line
        $this->content->text = substr($this->content->text, 0, strrpos($this->content->text, "\n"));
        return $this->content;
    }

    // my moodle can only have SITEID and it's redundant here, so take it away
    public function applicable_formats() {
        return array('all' => false,
                     'site' => true,
                     'site-index' => true,
                     'course-view' => false, 
                     'course-view-social' => false,
                     'mod' => false, 
                     'mod-quiz' => false);
    }

    public function instance_allow_multiple() {
          return false;
    }

    function has_config() {return true;}

    function instance_delete() {
        global $DB;
        $DB->delete_records('block_notify', array('courseid'=>1));
    }

    public function cron() {
            mtrace( "Hey, my cron script is running" );
            // do something
            return true;
    }
}
