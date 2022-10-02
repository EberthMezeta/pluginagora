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

        //print_object($files);

        $mform = &$this->_form;
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'fileslist');
        $mform->setType('blockid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        foreach ($files as $file) {
            $mform->addElement('checkbox', "aver-".$file->filename, $file->filename,null, array('fileid' => $file->id));
        }

        

        /*
        $radioarray = array();
        $radioarray[] = $mform->createElement('radio', 'yesno', '', get_string('yes'), 1);
        $radioarray[] = $mform->createElement('radio', 'yesno', '', get_string('no'), 0);
        $radioarray[] = $mform->createElement('radio', 'yesno', '', 're', 3);
        $mform->addGroup($radioarray, 'radioar', '', array(' '), false);

        $checkbox = array();

        foreach ($files as $file) {
            $checkbox[] = $mform->createElement('radio', 'cheked','', $file->filename,["contextid" => $file->contextid,"component"=>$file->component,"filearea"=>$file->filearea]);
        }
        $mform->addGroup($checkbox, 'checkbo', '', array(' '), true);
        */
        /*
        $mform->addElement('advcheckbox', 'test1', 'Test 1', null, array('group' => 1));
        $mform->addElement('advcheckbox', 'test2', 'Test 2', null, array('group' => 1));
        $this->add_checkbox_controller(1);
        $mform->addElement('advcheckbox', 'test3', 'Test 3', null, array('group' => 2));
        $mform->addElement('advcheckbox', 'test4', 'Test 4', null, array('group' => 2));
        $this->add_checkbox_controller(2, get_string("checkallornone"), array('style' => 'font-weight: bold;'), 1);
        $mform->setDefault('test3', 1);
        $mform->setDefault('test4', 1);
        
        */
        $this->add_action_buttons();
    }
}
