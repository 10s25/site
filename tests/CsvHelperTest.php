<?php
declare(strict_types=1);
require_once 'UnitTest.php';
require '../src/CsvHelper.php';

/**
 * Unit tests for CsvHelper class
 */
class CsvHelperTest extends UnitTest
{
    /**
     * @throws Exception
     */
    public function testLoadCsv(): void
    {
        $catGroups = [
            'test_group' => [
                'filepath' => 'data/groupes_test.csv',
                'fields' => ['nom', 'rs_icon', 'nom_rs', 'url']
            ]
        ];
        $expected =  array('test_group' =>
            array(
                0 => array(
                    'nom' => 'ÉTUDIANTS',
                    'rs_icon' => 'telegram',
                    'nom_rs' => 'dixseptembreetudiant',
                    'url' => 'https://t.me/dixseptembreetudiant'
                ),
                1 => array(
                    'nom' => 'MARCHE - 10 SEPTEMBRE',
                    'rs_icon' => 'telegram',
                    'nom_rs' => '+0jfTtV0l27RjZmU0',
                    'url' => 'https://t.me/+0jfTtV0l27RjZmU0'
                )
            )
        );

        $response = CsvHelper::loadCsv(
            catGroups: $catGroups,
            dataPath: dirname(path: __FILE__) .'/data/'
        );
        $this->printResult(testName: 'LoadCsv', passed: $response === $expected);
    }

    public function testLoadCsvFailedFileNotAccessible(): void
    {
        $catGroups = [
            'test_group' => [
                'filepath' => 'data/groupes_test_failed.csv',
                'fields' => ['nom', 'rs_icon', 'nom_rs', 'url']
            ]
        ];

        try {
            CsvHelper::loadCsv(
                catGroups: $catGroups,
                dataPath: dirname(path: __FILE__) .'/data/'
            );
        } catch (Exception $e) {
            $this->printResult(
                testName: 'LoadCsvFailedFileNotAccessible',
                passed: $e->getMessage() ===
                    sprintf(
                        CsvHelper::MESS_FICHIER_CSV_NOT_FOUND,
                        'groupes_test_failed.csv'
                    )
            );
        }
    }

    public function testLoadCsvFailedFileBadFormat(): void
    {
        $catGroups = [
            'test_group' => [
                'filepath' => 'data/groupes_test.csv',
                'fields' => ['test', 'test', 'test', 'test']
            ]
        ];

        try {
            CsvHelper::loadCsv(
                catGroups: $catGroups,
                dataPath: dirname(path: __FILE__) .'/data/'
            );
        } catch (Exception $e) {
            $this->printResult(
                testName: 'LoadCsvFailedBadFormat',
                passed: $e->getMessage() ===
                    sprintf(
                        CsvHelper::MESS_CSV_NOT_COMPLIANT,
                        'data/groupes_test.csv',
                        'test, test, test, test'
                    )
            );
        }
    }

    public function run(): void
    {
        try {
            $this->testLoadCsv();
        } catch (Exception $e) {
            $this->printResult(testName: 'LoadCsv', passed: false);
            print_r("\033[31m".$e->getMessage()."\033[0m\n");
        }
        $this->testLoadCsvFailedFileNotAccessible();
        $this->testLoadCsvFailedFileBadFormat();
    }
}

$test = new CsvHelperTest();
$test->run();