<?php

class block_pluginagora extends block_base {

    function init() {
        
        $this->title = get_string('pluginagora', 'block_pluginagora');
    }

    function get_content() {
        global $CFG, $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         =  new stdClass;
        $url = new moodle_url('/blocks/pluginagora/view.php', array('blockid' => $this->instance->id,'courseid' => $COURSE->id));
        $this->content->footer = html_writer::link($url, "Subir documentos a agora");

        return $this->content;
    }


}