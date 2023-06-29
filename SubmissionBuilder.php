<?php
namespace App;

require 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

class SubmissionBuilder
{
    private $batchSize;
    private $templateDir = "templates/";
    private $templates = [];
    private $students = [];
    private $submissions = [];

    public function __construct($batchSize = 10)
    {
        $this->batchSize = $batchSize;

        // load the templates
        $this->loadTemplates();

        // load the students from a csv file
        $this->loadStudents();
    }

    private function loadTemplates() {
        $templates = glob($this->templateDir . "*.pdf");
        foreach ($templates as $template) {
            $this->templates[] = $template;
        }
    }

    private function loadStudents() {
        $row = 1;
        if (($handle = fopen("files/roster.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row != 1) {
                    $this->students[] = $data[0];
                }

                $row++;
            }

            fclose($handle);
        }
    }

    public function buildSubmissions() {
        foreach ($this->students as $index => $studentId) {
            $this->buildSubmission($studentId);
        }

        $this->createBatchFiles();
    }

    private function createBatchFiles() {

        // chunk the files into batches of 10
        $chunks = array_chunk($this->submissions, $this->batchSize);

        $batchNumber = 1;
        foreach ($chunks as $chunk) {

            // get the page count
            $this->mergeSubmissions($chunk, $batchNumber);

            $batchNumber++;
        }

    }

    private function mergeSubmissions($submissions, $batchNumber) {
        // initiate FPDI
        $pdf = new Fpdi();

        // iterate through the submissions
        foreach ($submissions as $index => $submission) {
            // get the page count
            $pageCount = $pdf->setSourceFile($submission);

            // Loop through each page to preserve all pages
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                // add a page
                $pdf->AddPage();

                // import a page
                $templateId = $pdf->importPage($pageNo);

                // use the imported page and place it at point 0,0 with the same width and height as the used page
                $pdf->useTemplate($templateId);

            }
        }

        // Output the new PDF
        $batch = 'files/batches/' . $batchNumber . '.pdf';
        $pdf->Output('F', $batch);
    }

    private function buildSubmission($id) {
        // get the id image path
        $idImagePath = "files/signatures/" . $id . "-id.png";

        // get the name image path
        $nameImagePath = "files/signatures/" . $id . "-name.png";

        // initiate FPDI
        $pdf = new Fpdi();

        // set the source file
        $source = $this->templates[array_rand($this->templates)];
        $pageCount = $pdf->setSourceFile($source);

        // Loop through each page to preserve all pages
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

            // add a page
            $pdf->AddPage();

            // import a page
            $templateId = $pdf->importPage($pageNo);

            // use the imported page and place it at point 0,0 with the same width and height as the used page
            $pdf->useTemplate($templateId);

            // check if it is the first page (or any other page you want)
            if ($pageNo == 1) {

                // Now place the name image 840px wide and 180px tall
                $nameX = 38;
                $nameY = 6;
                $nameShrink = 0.1;
                $pdf->Image($nameImagePath, $nameX, $nameY, 840*$nameShrink, 180*$nameShrink);

                $idX = 140;
                $idY = 5;
                $idShrink = 0.1;
                // and place the id image
                $pdf->Image($idImagePath, $idX, $idY, 450*$idShrink, 130*$idShrink);

            }


        }

        // Output the new PDF
        $submission = 'files/submissions/' . $id . '.pdf';
        $pdf->Output('F', $submission);
        $this->submissions[] = $submission;

    }
}