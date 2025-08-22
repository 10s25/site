<?php
declare(strict_types=1);

class CsvHelper
{
    const MESS_FICHIER_CSV_NOT_FOUND = 'Fichier CSV non accessible: %s';
    const MESS_CANT_OPEN_CSV = 'Impossible d\'ouvrir le fichier CSV: %s';
    const MESS_CSV_NOT_COMPLIANT = "\nFichier CSV non conforme: %s." .
    "\nFormat attendu: [%s]\n";

    /**
     * Retourne un tableau structuré pour la génération du html
     * On exécute cette fonction UNE SEULE FOIS : pas besoin de relire les CSV plusieurs fois
     *
     * @param array $catGroups
     * @param string $dataPath
     * @return array
     * @throws Exception
     */
    public static function loadCSV(array $catGroups, string $dataPath): array
    {
        $layers = [];
        foreach ($catGroups as $cat_name => $cat_description ) {
            $cat_array = self::readCSV(
                csvFilename: $dataPath . basename(path: $cat_description['filepath'])
            );
            $fields = $cat_description['fields'];

            // vérif structure des fichiers CSV
            if (($fields !== $cat_array[0]) ){
                throw new Exception(message:
                    sprintf(
                        self::MESS_CSV_NOT_COMPLIANT,
                        $cat_description['filepath'],
                        implode(separator: ', ', array: $cat_description['fields'])
                    )
                );
            }
            // On enlève les noms des champs (1ère ligne du CSV)
            $cat_array = array_slice(array: $cat_array, offset: 1);
            $nb_fields = count(value: $fields);
            $nb_records = count(value: $cat_array);
            $records = [];
            // On lit tous les enregistrements de la catégorie de groupes
            // dans un tableau structuré avec les noms des champs:
            $layers += [
                $cat_name => self::getRecords(
                    records: $records,
                    nb_records: $nb_records,
                    nb_fields: $nb_fields,
                    fields: $fields,
                    cat_array: $cat_array
                )
            ];
        }

        return $layers;
    }

    /**
     * @throws Exception
     */
    public static function readCSV($csvFilename): array
    {
        // Validation du chemin de fichier
        if (!file_exists(filename: $csvFilename) || !is_readable(filename: $csvFilename)) {
            throw new Exception(
                message: sprintf(
                    self::MESS_FICHIER_CSV_NOT_FOUND,
                    basename(path: $csvFilename)
                )
            );
        }
        $file = fopen(filename: $csvFilename, mode: 'r');
        if ($file === false) {
            throw new Exception(
                message: sprintf(
                    self:: MESS_CANT_OPEN_CSV,
                    basename(path: $csvFilename)
                )
            );
        }
        $array = [];
        while (($line = fgetcsv(stream: $file)) !== FALSE) {
            $array[] = $line;
        }
        fclose(stream: $file);

        return $array;
    }

    private static function getRecords(
        array $records,
        int $nb_records,
        int $nb_fields,
        array $fields,
        array $cat_array
    ): array {
        for ($i = 0; $i < $nb_records; $i++) {
            $ca = [];
            for ($j = 0; $j < $nb_fields; $j++) {
                $ca += [$fields[$j] => htmlspecialchars(
                    string: $cat_array[$i][$j],
                    flags: ENT_QUOTES,
                    encoding: 'UTF-8',
                    double_encode: false
                )];
            }
            $records += [$i => $ca];
        }

        return $records;
    }
}
