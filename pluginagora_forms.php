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
        $mform->addElement('header', 'headerplugin', 'Subir archivos');
        $mform->addElement('text', 'usuario', 'Usuario'); // Add elements to your form.
        //$mform->setType('text', PARAM_NOTAGS);                   // Set type of element.
        //$mform->setDefault('text', 'Ingrese su usuario de agora');        // Default value.
        $mform->addElement('password', 'password', 'Contraseña');
        //$mform->setType('text', PARAM_NOTAGS);   
        $mform->addElement('button', 'loginBTN', "Iniciar Sesión");
        $mform->addElement('button', 'logoutBTN', "Cerrar sesión");
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'fileslist');
        $mform->addElement('hidden', 'token');

        $mform->setType('blockid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        foreach ($files as $file) {
            $mform->addElement('checkbox', "aver-".$file->filename, $file->filename,null, array('fileid' => $file->id));
        }

        $this->add_action_buttons();
    }
}
