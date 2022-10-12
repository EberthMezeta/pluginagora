<?php

require_once("{$CFG->libdir}/formslib.php");
require_once('../../config.php');



class pluginagora_form extends moodleform
{

    function definition()
    {
        global $DB, $COURSE;

        $sql = 'SELECT cm.id, cm.course, cm.module, mdl.name AS type, 
        CASE 
            WHEN mf.name IS NOT NULL THEN mf.name
            WHEN mb.name IS NOT NULL THEN mb.name
            WHEN mr.name IS NOT NULL THEN mr.name
            WHEN mu.name IS NOT NULL THEN mu.name
            WHEN mq.name IS NOT NULL THEN mq.name
            WHEN mp.name IS NOT NULL THEN mp.name
            WHEN ml.name IS NOT NULL THEN ml.name
            ELSE NULL
        END AS activityname, mr.intro as description,
        f.id AS fileid, f.filepath, f.filename, f.userid AS fileuserid, f.filesize, f.mimetype, f.author AS fileauthor, f.timecreated, f.timemodified
        FROM {course_modules} AS cm
        INNER JOIN {context} AS ctx ON ctx.contextlevel = 70 AND ctx.instanceid = cm.id
        INNER JOIN {modules} AS mdl ON cm.module = mdl.id
        LEFT JOIN {forum} AS mf ON mdl.name = "forum" AND cm.instance = mf.id
        LEFT JOIN {book} AS mb ON mdl.name = "book" AND cm.instance = mb.id
        LEFT JOIN {resource} AS mr ON mdl.name = "resource" AND cm.instance = mr.id
        LEFT JOIN {url} AS mu ON mdl.name = "url" AND cm.instance = mu.id
        LEFT JOIN {quiz} AS mq ON mdl.name = "quiz" AND cm.instance = mq.id
        LEFT JOIN {page} AS mp ON mdl.name = "page" AND cm.instance = mp.id
        LEFT JOIN {lesson} AS ml ON mdl.name = "lesson" AND cm.instance = ml.id
        LEFT JOIN {files} AS f ON f.contextid = ctx.id
        WHERE cm.course = ? AND mdl.name = ? AND filename != "."
        ';

        $files = $DB->get_records_sql($sql, [$COURSE->id, 'resource']);

        //$files = $DB->get_records_sql('SELECT * FROM {files} WHERE userid = ? AND filearea = ? AND filename != ?', [$userid, 'content', '.']);

        //print_object($files);

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
        $mform->addElement('hidden', 'iduser');

        $mform->setType('blockid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        foreach ($files as $file) {
            $mform->addElement('checkbox', "aver-" . $file->filename, $file->activityname, null, array('fileid' => $file->fileid));
        }

        $this->add_action_buttons(true, 'Subir archivos');
    }
}
