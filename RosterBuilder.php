<?php
namespace App;

require 'vendor/autoload.php';

use Faker\Factory;

class RosterBuilder
{
    public $faker;
    private $ids = [];
    private $usedIds = [];
    private $students = [];

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->loadUsedIds();
    }

    private function loadUsedIds() {
        $row = 1;
        if (($handle = fopen("used_ids.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $this->usedIds[] = $data[$c];
                }
            }
            fclose($handle);
        }
    }

    private function generateIds($count) {
        for ($i=0; $i < $count; $i++) {
            $this->ids[] = $this->generateUnigueId();
        }

        // sort the ids
        sort($this->ids);
    }

    private function generateUnigueId() {

        // generate a random 10 digit number
        do {
            $id = mt_rand(1000000000, 9999999999);
        } while(strlen((string)$id) < 10);

        // if the id is already in the array, generate a new one
        if (in_array($id, $this->ids) || in_array($id, $this->usedIds)) {
            return $this->generateUnigueId();
        }

        // otherwise, return the id
        return $id;
    }

    private function generateStudents() {
        foreach ($this->ids as $id) {
            $this->students[] = $this->generateStudent($id);
        }
    }

    private function generateStudent($id) {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        return [
            'username' => $id,
            'password' => 'p2d1234',
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => strtolower($firstName . '.' . $lastName . '.' . $id . '@codehaus.se'),
            'id' => $id
        ];
    }

    private function writeRosterFile() {
        $file = fopen('files/roster.csv', 'wa+');
        fputcsv($file, ['Username', 'Last Name', 'First Name', 'Email', 'New Password', 'Student ID']);

        foreach ($this->students as $student) {
            fputcsv($file, [$student['username'], $student['lastName'], $student['firstName'], $student['email'], $student['password'], $student['id']]);
        }

        fclose($file);
    }

    private function addIdsToUsedIdsFile() {
        $file = fopen('used_ids.csv', 'a+');

        foreach ($this->ids as $id) {
            fputcsv($file, [$id]);
        }

        fclose($file);
    }

    public function buildRoster($studentCount) {

        // build a list of unique ids
        $this->generateIds($studentCount);

        // use the ids to generate a students
        $this->generateStudents();

        // write the students to a CSV
        $this->writeRosterFile();

        // add the ids to the used ids file
        //$this->addIdsToUsedIdsFile();
    }
}