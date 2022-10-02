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

$userid = $USER->id; //obtenemos el id del usuario esta madre es 2

$PAGE->set_url('/blocks/pluginagora/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading("Plugin Agora");
$PAGE->requires->js(new moodle_url($CFG->wwwwroot . '/blocks/pluginagora/main.js'));
/*
$settingsnode = $PAGE->settingsnav->add(get_string('pluginagora_settings', 'block_pluginagora'));
$editurl = new moodle_url('/blocks/pluginagora/view.php', array('id' => $id,  'courseid' => $courseid ,'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('pluginagora_edit', 'block_pluginagora'), $editurl);
$editnode->make_active();
*/

$form = new pluginagora_form();
$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$toform['fileslist'] = [];
$form->set_data($toform);

//echo $OUTPUT->header();
//$form->display();
//echo $OUTPUT->footer();
if ($form->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($fromform = $form->get_data()) {

    print_object($fromform);
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
    );

    foreach ($list_ids as $id) {
        $file = $fs->get_file_by_id($id);
        $contenthash = $file->get_contenthash();
        $postData["file"] =  new CURLFile($CFG->dataroot . '/filedir/' . substr($contenthash, 0, 2) . '/' . substr($contenthash, 2, 2) . '/' . $contenthash);
        $postData["filename"] = $file->get_filename();
        $postData["size"] = $file->get_filesize();

        $extension = pathinfo($file->get_filename(), PATHINFO_EXTENSION);
        $postData["extension"] = $extension;
  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/api/catch");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        print_r($output);
        curl_close($ch);
    }




    /*
    foreach ($list_contextids as $contextid) {
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'mod_resource', 'content');
        foreach ($files as $f) {
            // $f is an instance of stored_file
            if ($f->get_filename() != '.') {
                $files_list[] = $f;

                $contextid = $f->get_contextid();
                $component = $f->get_component();
                $filearea = $f->get_filearea();
                $itemid = $f->get_itemid();
                $filepath = $f->get_filepath();
                $filename = $f->get_filename();

                $contenthash = $f->get_contenthash();

                $paths[] = $CFG->dataroot . '/filedir/' . substr($contenthash, 0, 2) . '/' . substr($contenthash, 2, 2) . '/' . $contenthash;

            }
        }
    }
    */
    //print_r($paths);
    //print_r($files_list);

    /*
    $postData = array(
        "file" =>  new CURLFile($paths[0]),
        "filename" => $files_list[0]->get_filename(),
    );
    print_r($postData);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/api/catch");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $output = curl_exec($ch);

    print_r($output);
    */
} else {
    $site = get_site();
    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}
