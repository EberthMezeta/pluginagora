<?php

require_once("{$CFG->libdir}/formslib.php");
require_once('../../config.php');



class pluginagora_form extends moodleform
{

    function definition()
    {
        global $DB, $OUTPUT, $PAGE, $USER;
        $userid = $USER->id;
        $files = $DB->get_records_sql('SELECT * FROM {files} WHERE userid = ? AND filearea = ? AND filename != ?', [$userid, 'content', '.']);

        $mform = &$this->_form;
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'fileslist');
        $mform->setType('blockid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        foreach ($files as $file) {
            $mform->addElement('checkbox', "aver-".$file->filename, $file->filename,null, array('fileid' => $file->id));
        }

        $this->add_action_buttons();
    }
}
