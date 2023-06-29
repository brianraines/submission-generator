<?php
require 'vendor/autoload.php';
include 'RosterBuilder.php';
include 'SignatureBuilder.php';
include 'SubmissionBuilder.php';

use App\RosterBuilder;
use App\SignatureBuilder;
use App\SubmissionBuilder;

// Options & default values
$options = array(
    'student-count' => '20',
    'batch-size' => '10',
);

// Get options from command line input
$opt = getopt('', array('student-count:', 'batch-size:'));

// If options are provided, overwrite the default values
foreach($opt as $key=>$value) {
    if(isset($options[$key])) {

        if ($key == "batch-size" && $value > 50) {
            echo "Batch size cannot be greater than 50\n";
            exit();
        }

        $options[$key] = $value;
    }
}

// Define a Help function
function display_help(){
    echo "Help function:\n";
    echo "php script.php --student-count=[value] --batch-size=[value]\n";
    echo "Default values: \n";
    echo "student-count = 20, batch-size = 10\n";
}

// If help is requested, display the help function
if(isset($opt['help'])) {
    display_help();
}

print_r($options);

resetBatches();

// instantiate the RosterBuilder class
$rosterBuilder = new RosterBuilder();

// generate a CSV with the roster of new students
// TODO generate course csv
$rosterBuilder->buildRoster($options['student-count']);

// generate signatures
$signatureBuilder = new SignatureBuilder();
$signatureBuilder->buildSignatures();

// build submission files
$submissionBuilder = new SubmissionBuilder($options['batch-size']);
$submissionBuilder->buildSubmissions();

clean();

function clean() {
    // remove signature files
    $signatures = glob('files/signatures/*.png'); // get all file names
    $submissions = glob('files/submissions/*.pdf'); // get all file names
    $files = array_merge($signatures, $submissions);

    foreach($files as $file){ // iterate files
        if(is_file($file))
            unlink($file); // delete file
    }
}

function resetBatches() {
    $batches = glob('files/batches/*.pdf'); // get all file names

    foreach($batches as $file){ // iterate files
        if(is_file($file))
            unlink($file); // delete file
    }
}
