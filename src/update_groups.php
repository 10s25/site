<?php
require 'CsvHelper.php';
error_reporting(error_level: E_ALL);
/**
 * Génère la liste des groupes
 */

const MESS_FILE_WRITTING_ERROR = 'Impossible d\'écrire le fichier %s';

// Catégories de groupes
const CATEGORIE_GROUPE = [
	'Réseaux sociaux' => [
		'fields'   => ['rs_icon', 'nom_rs', 'url'],
		'filepath' => 'groupes_nat.csv'
	],
	'Monde' => [
		'fields'   => ['nom', 'rs_icon', 'nom_rs', 'url', 'latitude', 'longitude'],
		'filepath' => 'groupes_monde.csv'
	],
	'Autres groupes' => [
		'fields'   => ['nom', 'rs_icon', 'nom_rs', 'url'],
		'filepath' => 'groupes_autres.csv'
	],
	'Régions' => [
		'fields'   => ['code_insee_region', 'nom', 'rs_icon', 'nom_rs', 'url', 'latitude', 'longitude'],
		'filepath' => 'groupes_reg.csv',
		'child'    => ['Départements' => 'code_insee_region']
	],
	'Départements' => [
		'fields'   => ['code_insee_region', 'code_insee_dep', 'nom', 'rs_icon', 'nom_rs', 'url', 'latitude', 'longitude'],
		'filepath' => 'groupes_dep.csv',
		'ischild'  => true,
		'child'    => ['' => 'code_insee_dep']
	],
	'' => [
		'fields'   => ['code_insee_dep', 'nom', 'rs_icon', 'nom_rs', 'url', 'latitude', 'longitude'],
		'filepath' => 'groupes_loc.csv',
		'ischild'  => true,
	]
];

// Chemin absolu du présent script
$path = dirname(path: __FILE__) .'/';

// Chemin absolu du site
$rootPath = dirname(path: $path) .'/';

// Chemin absolu des données
$dataPath = $path . 'data/';

/**
 * Retourne un tableau trié par $field
 *
 * @param array $array
 * @param string $field
 * @return array
 */
function sort_by_field(array $array, string $field): array
{
	$result = [];
	$i = 0;
	foreach ($array as $rec) {
		$field_value = $rec[$field] ?? '';
		unset($rec[$field]);
		$result[$field_value][$i] = $rec;
		$i++;
	}

	return $result;
}

/**
 *  Trie les groupes et y ajoute les enfants
 *
 * @param array $catGroups
 * @param array $groups
 * @return array
 */
function sort_groups(array $catGroups, array $groups): array
{
	$sorted_groups = [];
	foreach ($catGroups as $cat_name => $cat_description ) {
		if (isset($cat_description['ischild']))
			break;
		// tri par nom
		$records = sort_by_field(array: $groups[$cat_name], field: 'nom');

		// S'il y a des enfants, on stocke le nom de layer et la clé
		unset($child_layer_name, $child_key_name);
		if (isset($cat_description['child'])) {
			$child_layer_name = key(array: $cat_description['child']);
			$child_key_name = $cat_description['child'][$child_layer_name];
		}

		// tri par rs_icon et ajout des enfants
		$sorted_recs = [];
		foreach($records as $nom => $rec) {
			$children = $sorted_children = [];
			if (isset($child_key_name, $child_layer_name)) {
				$child_key = $rec[key($rec)][$child_key_name];
				$children = array_filter(
                    array: $groups[$child_layer_name],
					callback: function ($rec) use ($child_key, $child_key_name) {
						return ($rec[$child_key_name] == $child_key);
					});
				$children = sort_by_field(array: $children, field: 'nom');
				foreach ($children as $i => $childs) {
					$sorted_children[$i] = sort_by_field(
                        array: $childs,
                        field: 'rs_icon'
                    );
				}
			}
			$item = ['item' => sort_by_field(array: $rec, field: 'rs_icon')];
			if (count(value: $children))
				$item += ['children' => $sorted_children];
			// tri par rs_icon et ajout des enfants
			$sorted_recs += [$nom => $item];
		}
		$sorted_groups += [$cat_name => $sorted_recs];
	}
	return $sorted_groups;
}

try {
    $records = CsvHelper::loadCSV(catGroups: CATEGORIE_GROUPE, dataPath: $dataPath);
    $groups = sort_groups(catGroups: CATEGORIE_GROUPE, groups: $records);
    // Remplit le template
    ob_start();
    include($path . 'templates/groupes.php');
    $html = ob_get_clean();

    // Validation et nettoyage du contenu HTML
    //$html = htmlspecialchars($html, ENT_QUOTES, 'UTF-8', false);

    $destPath = $rootPath . 'global/ssi/groupes.shtml';
    $destFile = fopen(filename: $destPath, mode: 'w');
    if ($destFile === false) {
        throw new Exception(
            message: sprintf(
                MESS_FILE_WRITTING_ERROR,
                $destPath
            )
        );
    }
    fwrite(stream: $destFile, data: $html);
    fclose(stream: $destFile);
} catch (Exception $e) {
    exit($e->getMessage());
}
