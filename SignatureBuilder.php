<?php
namespace App;

require 'vendor/autoload.php';

use GDText\Box;
use GDText\Color;

class SignatureBuilder
{
    private $fontDir = "fonts/";
    private $fonts = [];
    private $students = [];

    public function __construct()
    {
        // load the fonts
        $this->loadFonts();

        // load the students from a csv file
        $this->loadStudents();
    }

    private function loadFonts() {
        $fonts = glob($this->fontDir . "*.ttf");
        foreach ($fonts as $font) {
            $font = [
                "path" => $font,
                "id-size" => 24,
                "id" => [
                    "size" =>24,
                    "w-offset" => 0,
                    "h-offset" => 0
                ],
                "name" => [
                    "size" =>24,
                    "w-offset" => 0,
                    "h-offset" => 0
                ]
            ];

            // adjust the fontsize for the font

            switch (basename($font['path'])) {

                case 'font2.ttf':
                    $font['id']['size'] = 64;
                    $font['id']['h-offset'] = 0;

                    $font['name']['size'] = 100;
                    $font['name']['h-offset'] = 50;
                    break;

                case 'font4.ttf':
                    $font['id']['size'] = 64;
                    $font['id']['h-offset'] = 0;

                    $font['name']['size'] = 120;
                    $font['name']['h-offset'] = 35;
                    break;

                case 'font5.ttf':
                    $font['id']['size'] = 84;
                    $font['id']['h-offset'] = 0;

                    $font['name']['size'] = 84;
                    $font['name']['h-offset'] = 70;
                    break;

                case 'font11.ttf':
                    $font['id']['size'] = 64;
                    $font['id']['h-offset'] = 0;

                    $font['name']['size'] = 80;
                    $font['name']['h-offset'] = 60;
                    break;

                case 'font12.ttf':
                    $font['id']['size'] = 64;
                    $font['id']['h-offset'] = 20;

                    $font['name']['size'] = 72;
                    $font['name']['h-offset'] = 60;
                    break;

                case 'font13.ttf':
                    $font['id']['size'] = 124;
                    $font['id']['h-offset'] = 0;

                    $font['name']['size'] = 124;
                    $font['name']['h-offset'] = 50;
                    break;

                case 'font14.ttf':
                    $font['id']['size'] = 56;
                    $font['id']['h-offset'] = 10;

                    $font['name']['size'] = 92;
                    $font['name']['h-offset'] = 50;
                    break;

                default:

                    break;
            }

            // add the font to the array
            $this->fonts[] = $font;
        }
    }

    private function loadStudents() {
        $row = 1;
        if (($handle = fopen("files/roster.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row != 1) {
                    $this->students[] = [
                        'firstName' => $data[2],
                        'lastName' => $data[1],
                        'id' => $data[0]
                    ];
                }

                $row++;
            }

            fclose($handle);
        }
    }

    public function buildSignatures() {
        foreach ($this->students as $index => $student) {
            $this->buildSignatureFiles($index, $student);
        }
    }

    private function buildSignatureFiles($index, $student) {
        // write the signature to a file  840px wide and 180px tall
        $this->generateImageFromText($student["id"] . "-name", $student["firstName"] . " " . $student["lastName"], 850, 180);

        // write the id to a file 450 x 130
        $this->generateImageFromText($student["id"] . "-id", $student["id"], 450, 130);

    }

    private function generateImageFromText($filename, $text, $width = 250, $height = 100) {
        // create a new image
        $im = imagecreatetruecolor($width, $height);

        // set the background color to transparent
        imagesavealpha($im, true);
        $transparentColor = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $transparentColor);

        // set the background color to white
        //$backgroundColor = imagecolorallocate($im, 255, 255, 255);
        //imagefill($im, 0, 0, $backgroundColor);

        // Randomly pick a font
        $font = $this->fonts[array_rand($this->fonts)];

        $box = new Box($im);
        $box->setFontFace($font['path']);

        if (str_ends_with($filename, '-id')) {
            $size = $font['id']['size'];
            $woffset = $width-$font['id']['w-offset'];
            $hoffset = $height-$font['id']['h-offset'];
        }

        if (str_ends_with($filename, '-name')) {
            $size = $font['name']['size'];
            $woffset = $width-$font['name']['w-offset'];
            $hoffset = $height-$font['name']['h-offset'];
        }

        $box->setFontSize($size);
        $box->setFontColor(new Color(0, 0, 0));
        $box->setTextAlign('left', 'bottom');

        $box->setBox(10, 10, $woffset, $hoffset);
        $box->draw($text);

        // Save image as PNG
        $filename = "files/signatures/" . $filename . ".png";
        imagepng($im, $filename);
        imagedestroy($im);
    }
}