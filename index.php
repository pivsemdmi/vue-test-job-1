<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


//echo "<pre>".print_r($_SERVER)."</pre>";

if (! isset($_GET['action']) && !isset($_POST['action'])) {
    die(file_get_contents(__DIR__.'/index.html'));
} else {
    $action = $_GET['action'] ?? $_POST['action'];

    require __DIR__ . '/Data.php';
    $data = new Data();

    if ($action === 'get') {
        $result = $data->get();
    } else if ($action === 'update') {
        $result = $data->update();
    } else if ($action === 'delete') {
        $result = $data->delete();
    } else {
        $result = 'Unknown action';
    }

    die($result);
}
