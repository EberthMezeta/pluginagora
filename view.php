<?php

require_once('../../config.php');
require_once('pluginagora_forms.php');

global $DB, $OUTPUT, $PAGE, $USER, $CFG;

$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

$contextid = context_course::instance($courseid)->id;


if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_pluginagora', $courseid);
}

require_login($course);

$userid = $USER->id;

$PAGE->set_url('/blocks/pluginagora/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading("Plugin Agora");
$PAGE->requires->js(new moodle_url($CFG->wwwwroot . '/blocks/pluginagora/main.js'));

$form = new pluginagora_form();
$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$toform['fileslist'] = [];
$toform['token'] = "";
$toform['iduser'] = "";


$form->set_data($toform);

if ($form->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($fromform = $form->get_data()) {
    
    $string_ids = $fromform->fileslist;
    $list_ids = explode(',', $string_ids);


    $files_list = array();
    $paths = array();
    $fs = get_file_storage();

    $postData = array(
        "file" =>  "",
        "filename" => "",
        "size" => "",
        "title" => "",
        "description" => "",
        "comment" => "",
        "state" => "privado",
        "extension" => "",
        "id_user" => "",
    );

    $auto =  "Authorization: Bearer ". $fromform->token;


    $headers = array(
        $auto 
    );

    $files_sends = "";

    foreach ($list_ids as $id) {

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
        f.id AS fileid, f.filepath, f.filename,CONCAT("/filedir/", SUBSTRING(f.contenthash, 1, 2), "/", SUBSTRING(f.contenthash, 3, 2), "/", f.contenthash) AS filesystempath, f.userid AS fileuserid, f.filesize, f.mimetype, f.author AS fileauthor, f.timecreated, f.timemodified
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
        WHERE cm.course = ? AND mdl.name = ? AND filename != "." AND f.id = ?
        ';

        $file_from_bd = $DB->get_record_sql($sql, [$COURSE->id, 'resource', $id]);

        $file = $fs->get_file_by_id($id);
        
        //$contenthash = $file->get_contenthash();
        $postData["file"] =  new CURLFile($CFG->dataroot . $file_from_bd->filesystempath);
        //$postData["file"] =  new CURLFile($CFG->dataroot . '/filedir/' . substr($contenthash, 0, 2) . '/' . substr($contenthash, 2, 2) . '/' . $contenthash);
        $postData["filename"] = $file_from_bd->filename;
        $postData["size"] = $file_from_bd->filesize;
        $postData["title"] = $file_from_bd->activityname;
        $postData["description"] = strip_tags($file_from_bd->description);

        $extension = pathinfo($file->get_filename(), PATHINFO_EXTENSION);
        $postData["extension"] = $extension;
        $postData["id_user"] = $fromform->iduser;
        
  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/api/catch");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        curl_close($ch);

        if ($output == 1) {
            $files_sends.= "El arhivo '" .$file_from_bd->activityname . "' se ha subido correctamente <br>";
        }else{
            $files_sends.= "El arhivo '" .$file_from_bd->activityname . "' no se ha subido correctamente <br>";
        }
        
    }
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    echo $OUTPUT->header();
    echo $OUTPUT->heading("Lista de archivos enviados");
    echo $OUTPUT->box($files_sends);
    echo $OUTPUT->single_button($courseurl, "Volver al curso");
    echo $OUTPUT->footer();
    


} else {
    $site = get_site();
    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}
