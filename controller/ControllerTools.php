<?php

class ControllerTools extends Controller
{

	use CalcStats;

	const TRASH_PATH = "medias/trash/";

	public function savedb(){
		$timing_allow_backup = 60; // minutes
		$days_keeping_db = 90; // days
		$return = [
			'state' => false,
			'value' => ""
		];

		$files = glob("backup/db/*.sql");
		$time_last_backup = 0;
		if (is_array($files) && !empty($files)) {
			$time_last_backup = filemtime(max($files)); // le dernier fichier de sauvegarde
		}
		$headers = getallheaders();
		$token = str_replace('Bearer ', '', $headers['Authorization']);

		if (time() - $time_last_backup >= $timing_allow_backup * 60) {
			if (isset($token)) {
				if ($this->isTokenValid($token)) {

					$backup_path = dirname(__FILE__) . "/../backup/db/";
					$backup_path = str_replace("\\", "/", $backup_path);

					// check if mysqldump is available
					$output = array();
					exec('which mysqldump', $output);
					if (!empty($output)) {
						// mysqldump is available use it
						$text = "Les bases de données ont bien été enregistrées via mysqldump";
						exec("mysqldump --user={$GLOBALS["pdoLogin"]} --password={$GLOBALS["pdoPassword"]} {$GLOBALS["pdoName"]} > {$backup_path}{$GLOBALS["pdoName"]}_" . date("Y-m-d-H-i-s") . ".sql");
					} else {
						// mysqldump is not available use SELECT INTO OUTFILE
						try {
							$pdo = new PDO("mysql:host=" . $GLOBALS['pdoHost'] . ";dbname=" . $GLOBALS['pdoName'] . "", $GLOBALS["pdoLogin"], $GLOBALS["pdoPassword"]);
							$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
							$stmt = $pdo->query("SHOW TABLES");
							$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
							foreach ($tables as $table) {
								$file = $backup_path . $GLOBALS["pdoName"] . '_' . $table . '_' . date("Y-m-d-H-i-s") . ".sql";
								$stmt = $pdo->prepare("SELECT * INTO OUTFILE '$file' FROM $table");
								$stmt->execute();
							}
							$text = "Les bases de données ont bien été enregistrées via le système PDO";
						} catch (PDOException $e) {
							$text = "Erreur d'enregistrement des bases de données via le système PDO - " . $e->getMessage();
						}
					}

					// Suppression des sauvegardes plus anciennes d'un mois
					$files = glob($backup_path . '*.sql');
					$now   = time();
					$n = 0;
					foreach ($files as $file) {
						if (FileManager::isFile($file)) {
							if ($now - filemtime($file) >= $days_keeping_db * 24 * 60 * 60) {
								unlink($file);
								$n++;
							}
						}
					}

					ob_start(); ?>
					<div class='text-left'>
						<p><?= $text ?></p>
						<p>Il y a <?= $n ?> ancienne(s) base(s) de donnée(s) qui ont/a été supprimé</p>
					</div>
					<?php $return["value"] = ob_get_clean();
					$return["state"] = true;
				} else {
					$return["value"] = "Le token est invalide";
				}
			} else {
				$return["value"] = "Le token est manquant";
			}
		} else {
			$return["value"] = "La dernière sauvegarde a été faite il y a moins d'une heure.";
		}
		echo json_encode($return);
		flush();
	}

	// Fonction qui vérifie si les pages obligatoires sont présentes dans la base de donnée, si non elle les créait.
	public function verifAndCreatePageNeeded(){
		$return = [
			'state' => false,
			'value' => "<p>"
		];

		$headers = getallheaders();
		$token = str_replace('Bearer ', '', $headers['Authorization']);
		if (isset($token)) {
			if ($this->isTokenValid($token)) {

				$manager = new PageManager();
				$managerS = new SectionManager();
				$n = false;
				foreach (Page::UNIQID_NO_EDIT as $name => $uniqid) {
					if (!$manager->existsUniqid($uniqid)) {
						$n = true;
						switch ($name) {
							case 'home':
								$name_page = "Accueil";
								$is_editable = true;
								$public = true;
								$return["value"] .= "La page d'accueil n'existe pas, elle a été créée.<br>";
								break;
							case 'gestion_des_pages':
								$name_page = "Gestion des pages";
								$is_editable = false;
								$public = false;
								$section = new Section([
									"uniqid" => uniqid(),
									"type" => "page.php"
								]);
								$section->setTimestamp_add(time());
								$section->setTimestamp_updated(time());
								$return["value"] .= "La page de gestion des pages n'existe pas, elle a été créée.<br>";
								break;
							case 'cgu':
								$name_page = "CGU";
								$is_editable = false;
								$public = true;
								$section = new Section([
									"uniqid" => uniqid(),
									"type" => "view_file.php",
									"title" => "Conditions générales d'utilisation",
									"content" => "medias/regulatory/cgu.pdf"
								]);
								$section->setTimestamp_add(time());
								$section->setTimestamp_updated(time());
								$return["value"] .= "La page des CGU n'existe pas, elle a été créée.<br>";
								break;
							case 'user_manager':
								$name_page = "Gestion des utilisateurs·trices";
								$is_editable = false;
								$public = false;
								$section = new Section([
									"uniqid" => uniqid(),
									"type" => "user.php"
								]);
								$section->setTimestamp_add(time());
								$section->setTimestamp_updated(time());
								$return["value"] .= "La page des Utilisateurs·trices n'existe pas, elle a été créée.<br>";
								break;

							default:
								$name_page = "";
								break;
						}
						if (!empty($name_page)) {
							$page = new Page([
								'uniqid' => $uniqid,
								'name' => $name_page,
								'url_name' => $name,
								'is_editable' => $is_editable,
								'public' => $public,
								'category' => -1,
								"is_dropdown" => false
							]);
							$page->setTimestamp_add(time());
							$page->setTimestamp_updated(time());
							$manager->add($page);

							if (isset($section)) {
								$section->setUniqid_page($page->getUniqid());
								$managerS->add($section);
							}
						}
					}
				}
				$return["state"] = true;
				$return["value"] .= "</p>";
				if (!$n) {
					$return["value"] = "Toutes les pages obligatoires sont présentes.";
				}
			} else {
				$return["value"] = "Le token est invalide";
			}
		} else {
			$return["value"] = "Le token est invalide";
		}

		echo json_encode($return);
		flush();
	}

	const TEMPLATE_ITEMS = "items";
	const TEMPLATE_MOBS = "mobs";
	const TEMPLATE_SPELLS = "spells";

	const VACUM_ITEMS_URL = 'https://api.dofusdb.fr/items?typeId%5B$ne%5D=203&$sort=level&lang=fr';
	const VACUM_MOBS_URL = 'https://api.dofusdb.fr/monsters?$sort[level]=1lang=fr';
	const VACUM_SPELLS_URL = 'https://api.dofusdb.fr/spells?lang=fr';
	const VACUM_CLASS_URL = 'https://api.dofusdb.fr/breeds?lang=fr';

	// --DEBUT - ITEMS CONVERSION
	const VACUM_ITEMS_CATEGORY = [
		1 => "Amulette",
		13 => "Parchemin d'expérience",
		15 => "Ressources diverses",
		17 => "Cape",
		20 => "Outil",
		33 => "Pain",
		34 => "Céréale",
		36 => "Plante",
		37 => "Bière",
		38 => "Bois",
		39 => "Minerai",
		42 => "Friandise",
		48 => "Poudre",
		54 => "Poil",
		58 => "Graine",
		60 => "Huile",
		68 => "Légume",
		75 => "Parchemin de sortilège",
		76 => "Parchemin de caractéristique",
		228 => "Liquide",
		3 => "Baguette",
		9 => "Anneau",
		12 => "Potion",
		25 => "Document",
		35 => "Fleur",
		47 => "Os",
		51 => "Pierre brute",
		66 => "Métaria",
		71 => "Matériel d'alchimie",
		78 => "Rune de forgemagie",
		104 => "Aile",
		164 => "Vêtement",
		2 => "Arc",
		5 => "Dague",
		29 => "Bénédiction",
		30 => "Malédiction",
		31 => "Roleplay Buffs",
		187 => "Viande primitive",
		6 => "Épée",
		14 => "Objet de dons",
		32 => "Personnage suiveur",
		41 => "Poisson",
		80 => "Objet de mission",
		18 => "Familier",
		19 => "Hache",
		26 => "Potion de forgemagie",
		61 => "Peluche",
		74 => "Fée d'artifice",
		16 => "Chapeau",
		82 => "Bouclier",
		85 => "Pierre d'âme pleine",
		89 => "Cadeau",
		230 => "Obsolète",
		173 => "Parchemin d'attitude",
		93 => "Objet d'élevage",
		100 => "Sac de ressources",
		10 => "Ceinture",
		11 => "Bottes",
		27 => "Objet de Mutation",
		59 => "Peau",
		79 => "Boisson",
		84 => "Clef",
		94 => "Objet utilisable",
		112 => "Prisme",
		284 => "Relique d'Incarnation",
		83 => "Pierre d'âme",
		110 => "Gelée",
		28 => "Nourriture boost",
		172 => "Coffre",
		43 => "Potion de téléportation",
		50 => "Pierre précieuse",
		154 => "Emballage",
		157 => "Figurine",
		231 => "Rabmablague",
		40 => "Alliage",
		53 => "Plume",
		95 => "Planche",
		245 => "Ressources obsolètes",
		185 => "Sève",
		88 => "Pierre magique",
		200 => "Parchemin de titre",
		206 => "Potion de monture",
		57 => "Laine",
		209 => "Nourriture pour familier",
		46 => "Fruit",
		219 => "Ressources des Songes",
		153 => "Nowel",
		211 => "Rune de transcendance",
		69 => "Viande comestible",
		214 => "Potion d'attitude",
		49 => "Poisson comestible",
		114 => "Arme magique",
		184 => "Conteneur",
		218 => "Popoche de Havre-Sac",
		299 => "Épaulières",
		199 => "Costume",
		232 => "Haïku",
		236 => "Mots de haïku",
		300 => "Ailes",
		259 => "Bouataklône",
		241 => "Ressources de Temporis",
		188 => "Parchemin d'émoticônes",
		216 => "Bourse",
		222 => "Parchemin d'ornement",
		226 => "Objet utilisable de Temporis",
		116 => "Aucune",
		122 => "Aucune",
		278 => "Ressources de Percepteur",
		297 => "Globe de lumière",
		7 => "Marteau",
		21 => "Pioche",
		23 => "Dofus",
		70 => "Teinture",
		87 => "Parchemin de recherche",
		63 => "Viande",
		22 => "Faux",
		109 => "Œil",
		4 => "Bâton",
		8 => "Pelle",
		103 => "Patte",
		183 => "Substrat",
		165 => "Potion de conquête",
		258 => "Gravure de forgemagie",
		56 => "Cuir",
		105 => "Œuf",
		271 => "Lance",
		169 => "Compagnon",
		174 => "Carte",
		175 => "Fragment de carte",
		107 => "Carapace",
		229 => "Ressource de combat",
		121 => "Montilier",
		167 => "Essence de gardien de donjon",
		96 => "Écorce",
		98 => "Racine",
		108 => "Bourgeon",
		55 => "Étoffe",
		151 => "Trophée",
		152 => "Galet",
		65 => "Queue",
		106 => "Oreille",
		99 => "Filet de capture",
		97 => "Certificat de Dragodinde",
		189 => "Orbe de forgemagie",
		190 => "Harnachements de Dragodinde",
		196 => "Certificat de Muldo",
		197 => "Caution",
		255 => "Harnachements de Muldo",
		207 => "Certificat de Volkorne",
		256 => "Harnachements de Volkorne",
		119 => "Champignon",
		111 => "Coquille",
		179 => "Préparation",
		176 => "Boîte de fragments",
		217 => "Prysmaradite",
		266 => "Ressources des Anomalies Temporelles",
		273 => "Fers de Percepteur",
		277 => "Tunique de Percepteur",
		275 => "Bannière de Percepteur",
		276 => "Poignards de Percepteur"
	];
	const VACUM_ITEMS_CATEGORY_TO_RESSOURCES = [
		15 => Ressource::TYPE_RESSOURCES_DIV,
		34 => Ressource::TYPE_CEREALE,
		36 => Ressource::TYPE_PLANTE,
		38 => Ressource::TYPE_BOIS,
		39 => Ressource::TYPE_MINERAI,
		48 => Ressource::TYPE_POUDRE,
		54 => Ressource::TYPE_POIL,
		58 => Ressource::TYPE_GRAINE,
		60 => Ressource::TYPE_HUILE,
		68 => Ressource::TYPE_LEGUME,
		228 => Ressource::TYPE_LIQUIDE,
		35 => Ressource::TYPE_FLEUR,
		47 => Ressource::TYPE_OS,
		51 => Ressource::TYPE_PIERRE_BRUTE,
		66 => Ressource::TYPE_METARIA,
		71 => Ressource::TYPE_MATERIEL_ALCHIMIE,
		104 => Ressource::TYPE_AILE,
		164 => Ressource::TYPE_VETEMENT,
		31 => Ressource::TYPE_ROLEPLAY_BUFFS,
		32 => Ressource::TYPE_PERSONNAGE_SUIVEUR,
		41 => Ressource::TYPE_POISSON,
		61 => Ressource::TYPE_PELUCHE,
		85 => Ressource::TYPE_PIERRE_AME_PLEINE,
		230 => Ressource::TYPE_OBSOLETE,
		93 => Ressource::TYPE_OBJET_ELEVAGE,
		100 => Ressource::TYPE_SAC_RESSOURCES,
		27 => Ressource::TYPE_OBJET_MUTATION,
		59 => Ressource::TYPE_PEAU,
		84 => Ressource::TYPE_CLEF,
		112 => Ressource::TYPE_PRISME,
		284 => Ressource::TYPE_RELIQUE_INCARNATION,
		83 => Ressource::TYPE_PIERRE_AME,
		110 => Ressource::TYPE_GELEE,
		172 => Ressource::TYPE_COFFRE,
		50 => Ressource::TYPE_PIERRE_PRECIEUSE,
		154 => Ressource::TYPE_EMBALLAGE,
		231 => Ressource::TYPE_RABMABLAGUE,
		40 => Ressource::TYPE_ALLIAGE,
		53 => Ressource::TYPE_PLUME,
		95 => Ressource::TYPE_PLANCHE,
		185 => Ressource::TYPE_SEVE,
		88 => Ressource::TYPE_PIERRE_MAGIQUE,
		57 => Ressource::TYPE_LAINE,
		46 => Ressource::TYPE_FRUIT,
		219 => Ressource::TYPE_RESSOURCES_SONGES,
		153 => Ressource::TYPE_NOWEL,
		184 => Ressource::TYPE_CONTENEUR,
		218 => Ressource::TYPE_POPOCHE_HAVRE_SAC,
		232 => Ressource::TYPE_HAIKU,
		259 => Ressource::TYPE_BOUATAKLONE,
		278 => Ressource::TYPE_RESSOURCES_PERCEPTEUR,
		297 => Ressource::TYPE_GLOBE_LUMIERE,
		63 => Ressource::TYPE_VIANDE,
		22 => Ressource::TYPE_FAUX,
		109 => Ressource::TYPE_OEIL,
		103 => Ressource::TYPE_PATTE,
		183 => Ressource::TYPE_SUBSTRAT,
		258 => Ressource::TYPE_GRAVURE_FORGEMAGIE,
		56 => Ressource::TYPE_CUIR,
		105 => Ressource::TYPE_OEUF,
		174 => Ressource::TYPE_CARTE,
		175 => Ressource::TYPE_FRAGMENT_CARTE,
		107 => Ressource::TYPE_CARAPACE,
		229 => Ressource::TYPE_RESSOURCE_COMBAT,
		167 => Ressource::TYPE_ESSENCE_GARDIEN_DONJON,
		96 => Ressource::TYPE_ECORCE,
		98 => Ressource::TYPE_RACINE,
		108 => Ressource::TYPE_BOURGEON,
		55 => Ressource::TYPE_ETOFFE,
		152 => Ressource::TYPE_GALET,
		65 => Ressource::TYPE_QUEUE,
		106 => Ressource::TYPE_OREILLE,
		99 => Ressource::TYPE_FILET_CAPTURE,
		97 => Ressource::TYPE_CERTIFICAT_DRAGODINDE,
		189 => Ressource::TYPE_ORBE_FORGEMAGIE,
		196 => Ressource::TYPE_CERTIFICAT_MULDO,
		197 => Ressource::TYPE_CAUTION,
		207 => Ressource::TYPE_CERTIFICAT_VOLKORNE,
		119 => Ressource::TYPE_CHAMPIGNON,
		111 => Ressource::TYPE_COQUILLE,
		176 => Ressource::TYPE_BOITE_FRAGMENTS,
		266 => Ressource::TYPE_RESSOURCES_ANOMALIES_TEMPORELLES,
	];
	const VACUM_ITEMS_CATEGORY_TO_CONSOMABLE = [
		13 => Consumable::TYPE_PARCHMENT,
		33 => Consumable::TYPE_FOOD,
		37 => Consumable::TYPE_DRINKS,
		42 => Consumable::TYPE_TREAT,
		75 => Consumable::TYPE_PARCHMENT,
		76 => Consumable::TYPE_PARCHMENT,
		12 => Consumable::TYPE_POTION,
		25 => Consumable::TYPE_PARCHMENT,
		187 => Consumable::TYPE_FOOD,
		14 => Consumable::TYPE_OTHER,
		80 => Consumable::TYPE_OTHER,
		26 => Consumable::TYPE_POTION,
		74 => Consumable::TYPE_OTHER,
		85 => Consumable::TYPE_STONE,
		89 => Consumable::TYPE_OTHER,
		173 => Consumable::TYPE_PARCHMENT,
		79 => Consumable::TYPE_DRINKS,
		94 => Consumable::TYPE_OTHER,
		28 => Consumable::TYPE_FOOD,
		43 => Consumable::TYPE_POTION,
		157 => Consumable::TYPE_OTHER,
		245 => Consumable::TYPE_OTHER,
		200 => Consumable::TYPE_PARCHMENT,
		206 => Consumable::TYPE_POTION,
		209 => Consumable::TYPE_FOOD,
		69 => Consumable::TYPE_FOOD,
		214 => Consumable::TYPE_POTION,
		49 => Consumable::TYPE_FOOD,
		236 => Consumable::TYPE_OTHER,
		241 => Consumable::TYPE_OTHER,
		188 => Consumable::TYPE_PARCHMENT,
		216 => Consumable::TYPE_OTHER,
		226 => Consumable::TYPE_OTHER,
		70 => Consumable::TYPE_OTHER,
		87 => Consumable::TYPE_PARCHMENT,
		165 => Consumable::TYPE_POTION,
		179 => Consumable::TYPE_OTHER,
		78 => Consumable::TYPE_STONE,
		211 => Consumable::TYPE_STONE,
		29 => Consumable::TYPE_MODIFIER,
		30 => Consumable::TYPE_MODIFIER
	];
	const VACUM_ITEMS_CATEGORY_TO_ITEM = [
		169 => Item::TYPE_COMPAGNON,
		1 => Item::TYPE_AMULETTE,
		17 => Item::TYPE_CAPE,
		20 => Item::TYPE_OUTIL,
		3 => Item::TYPE_BAGUETTE,
		9 => Item::TYPE_ANNEAU,
		2 => Item::TYPE_ARC,
		5 => Item::TYPE_DAGUE,
		6 => Item::TYPE_EPEE,
		19 => Item::TYPE_HACHE,
		16 => Item::TYPE_COIFFE,
		82 => Item::TYPE_BOUCLIER,
		10 => Item::TYPE_CEINTURE,
		11 => Item::TYPE_BOTTES,
		114 => Item::TYPE_ARME_MAGIQUE,
		7 => Item::TYPE_MARTEAU,
		21 => Item::TYPE_PIOCHE,
		23 => Item::TYPE_DOFUS,
		151 => Item::TYPE_TROPHEE,
		217 => Item::TYPE_OTHER,
		22 => Item::TYPE_FAUX,
		4 => Item::TYPE_BATON,
		8 => Item::TYPE_PELLE,
		271 => Item::TYPE_LANCE,
		121 => Item::TYPE_MONTURE,
		18 => Item::TYPE_FAMILIER,

		299 => ITEM::TYPE_OBJET_APPARAT,
		199 => ITEM::TYPE_OBJET_APPARAT,
		300 => ITEM::TYPE_OBJET_APPARAT,
		190 => ITEM::TYPE_OBJET_APPARAT,
		255 => ITEM::TYPE_OBJET_APPARAT,
		256 => ITEM::TYPE_OBJET_APPARAT,
		273 => ITEM::TYPE_OBJET_APPARAT,
		277 => ITEM::TYPE_OBJET_APPARAT,
		275 => ITEM::TYPE_OBJET_APPARAT,
		276 => ITEM::TYPE_OBJET_APPARAT,
		246 => ITEM::TYPE_OBJET_APPARAT,
		252 => ITEM::TYPE_OBJET_APPARAT,
		247 => ITEM::TYPE_OBJET_APPARAT,
		248 => ITEM::TYPE_OBJET_APPARAT,
		251 => ITEM::TYPE_OBJET_APPARAT,
		249 => ITEM::TYPE_OBJET_APPARAT,
		250 => ITEM::TYPE_OBJET_APPARAT,
		277 => ITEM::TYPE_OBJET_APPARAT,
	];

	const VACUM_ITEMS_SUPER_CATEGORY = [
		1 => 'Amulette',
		2 => 'Arme',
		6 => 'Consommable',
		9 => 'Ressource',
		11 => 'Cape',
		3 => 'Anneau',
		17 => 'Bénédiction',
		18 => 'Malédiction',
		19 => 'Bonus de jeu de rôle',
		20 => 'Suiveur',
		12 => 'Familier',
		7 => 'Bouclier',
		10 => 'Chapeau',
		4 => 'Ceinture',
		5 => 'Bottes',
		15 => 'Mutation',
		70 => 'Aucune',
		16 => 'Nourriture',
		25 => 'Costume',
		13 => 'Dofus / Trophée',
		23 => 'Compagnon',
		27 => 'Fantôme de familier',
		24 => 'Equipement de monture',
		69 => 'Aucune'
	];
	const VACUM_ITEMS_CARACTERISTICS_ID = [
		29 => "énergie",
		23 => "PM",
		1 => "PA",
		10 => "Force",
		12 => "Sagesse",
		13 => "Chance",
		14 => "Agilité",
		15 => "Intelligence",
		0 => "Effets divers",
		11 => "Points de vie",
		89 => "Dommages Feu",
		88 => "Dommages Terre",
		90 => "Dommages Eau",
		91 => "Dommages Air",
		92 => "Dommages Neutre",
		58 => "Résistance fixe Terre",
		54 => "Résistance fixe Neutre",
		55 => "Résistance fixe Feu",
		56 => "Résistance fixe Eau",
		57 => "Résistance fixe Air",
		33 => "Résistance Terre",
		34 => "Résistance Feu",
		35 => "Résistance Eau",
		36 => "Résistance Air",
		37 => "Résistance Neutre",
		-1 => "Infliges Dommages",
		40 => "Pods",
		48 => "Prospection",
		25 => "Puissance",
		38 => "Change l'apparence",
		81 => "Nb d'ennemie",
		44 => "Initiative",
		26 => "Invocations",
		16 => "Dommages",
		19 => "Portée",
		18 => "Critique",
		28 => "Esquive PM",
		78 => "Fuite",
		79 => "Tacle",
		69 => "Puissances pièges",
		70 => "Dommages pièges",
		82 => "Retrait PA",
		83 => "Retrait PM",
		27 => "Esquive PA",
		49 => "Augmentation des soins",
		84 => "Dommages de poussée",
		87 => "Résistance critique",
		86 => "Dommaes critiques",
		85 => "Résistance poussée",
		122 => "Dommages d'armes",
		123 => "Dommages aux sorts",
		124 => "Résistance mélée",
		121 => "Résistance distance",
		125 => "Dommmage mélée",
		120 => "Dommage distance",

	];
	const VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT = [
		23 => Creature::CARACTERISTICS['pm']['name'],
		1 => Creature::CARACTERISTICS['pa']['name'],
		10 => Creature::CARACTERISTICS['force']['name'],
		12 => Creature::CARACTERISTICS['sagesse']['name'],
		13 => Creature::CARACTERISTICS['chance']['name'],
		14 => Creature::CARACTERISTICS['agi']['name'],
		15 => Creature::CARACTERISTICS['intel']['name'],
		11 => Creature::CARACTERISTICS['life']['name'],
		89 => Creature::CARACTERISTICS['do_fixe_feu']['name'],
		88 => Creature::CARACTERISTICS['do_fixe_terre']['name'],
		90 => Creature::CARACTERISTICS['do_fixe_eau']['name'],
		91 => Creature::CARACTERISTICS['do_fixe_air']['name'],
		92 => Creature::CARACTERISTICS['do_fixe_neutre']['name'],
		58 => Creature::CARACTERISTICS['res_terre']['name'],
		54 => Creature::CARACTERISTICS['res_neutre']['name'],
		55 => Creature::CARACTERISTICS['res_feu']['name'],
		56 => Creature::CARACTERISTICS['res_eau']['name'],
		57 => Creature::CARACTERISTICS['res_air']['name'],
		33 => Creature::CARACTERISTICS['res_terre']['name'],
		34 => Creature::CARACTERISTICS['res_feu']['name'],
		35 => Creature::CARACTERISTICS['res_eau']['name'],
		36 => Creature::CARACTERISTICS['res_air']['name'],
		37 => Creature::CARACTERISTICS['res_neutre']['name'],
		44 => Creature::CARACTERISTICS['ini']['name'],
		26 => Creature::CARACTERISTICS['invocation']['name'],
		16 => Creature::CARACTERISTICS['do_fixe_multiple']['name'],
		19 => Creature::CARACTERISTICS['po']['name'],
		28 => Creature::CARACTERISTICS['dodge_pm']['name'],
		78 => Creature::CARACTERISTICS['fuite']['name'],
		79 => Creature::CARACTERISTICS['tacle']['name'],
		27 => Creature::CARACTERISTICS['dodge_pa']['name'],
	];
	// -- FIN - ITEMS CONVERSION

	public function getTotalElementFromDofusDB(){
		$return = [
			'state' => false,
			'value' => ""
		];

		$headers = getallheaders();
		$token = str_replace('Bearer ', '', $headers['Authorization']);

		if (isset($token)) {
			if ($this->isTokenValid($token)) {

				// return $this->getIdDofusDBSpellFromClass();

				if (isset($_REQUEST['template'])) {
					if (in_array($_REQUEST['template'], [self::TEMPLATE_ITEMS, self::TEMPLATE_MOBS, self::TEMPLATE_SPELLS])) {
						$url = null;
						switch ($_REQUEST['template']) {
							case self::TEMPLATE_ITEMS:
								$url = self::VACUM_ITEMS_URL;
								break;
							case self::TEMPLATE_MOBS:
								$url = self::VACUM_MOBS_URL;
								break;
							case self::TEMPLATE_SPELLS:
								$url = self::VACUM_SPELLS_URL;
								break;
							default:
								$return["value"] = "Le template est incorrect";
								break;
						}

						try {
							$content = @file_get_contents($url . '&$limit=0');
							if(!$content) {
								$return["value"] = "Erreur lors de la récupération du nombre total d'objet";
							} else {
								$content = json_decode($content, true);
								$return["value"] = $content['total'];
								$return["state"] = true;
							}
						} catch (\Throwable $th) {
							$return["value"] = "Erreur lors de la récupération du nombre total d'objet";
						}
					}
				} else {
					$return["value"] = "Le template est manquant";
				}
			} else {
				$return["value"] = "Le token est invalide";
			}
		} else {
			$return["value"] = "Le token est manquant";
		}

		echo json_encode($return);
		flush();
	}

	public function updatedbFromDofusDB(){
		$return = [
			'state' => false,
			'value' => "",
			'super_category_list' => [],
			'category_list' => []
		];

		$headers = getallheaders();
		$token = str_replace('Bearer ', '', $headers['Authorization']);

		if (isset($token)) {
			if ($this->isTokenValid($token)) {
				$continue = true;

				if (isset($_REQUEST['template'])) {
					if (in_array($_REQUEST['template'], [self::TEMPLATE_ITEMS, self::TEMPLATE_MOBS, self::TEMPLATE_SPELLS])) {
				
						// Définition de l'url de l'API
							$url = null;	
							switch ($_REQUEST['template']) {
								case self::TEMPLATE_ITEMS:
									$url = self::VACUM_ITEMS_URL;
									break;
								case self::TEMPLATE_MOBS:
									$url = self::VACUM_MOBS_URL;
									break;
								case self::TEMPLATE_SPELLS:
									$url = self::VACUM_SPELLS_URL;
									break;
								default:
									$return["value"] = "Le template est incorrect";
									break;
							}
							if (isset($_REQUEST['offset']) && isset($_REQUEST['limit'])) {
								$offset = $_REQUEST['offset'];
								$limit = $_REQUEST['limit'];
							} else {
								$return['value'] .= "L'offset ou la limite est incorrecte<br>";
								$continue = false;
							}
							if ($limit > 0 && isset($url)) {
								$url .= '&$skip=' . $offset . '&$limit=' . $limit;
								$return['value'] .= "API URL : " . $url . '<br>';
							} else {
								$return["value"] = "L'URL n'est pas valide ou la limite est incorrecte";
								$continue = false;
							}

						// Définition de l'écriture ou de l'affichage simple des données
							$write = false;
							if (isset($_REQUEST['write'])) {
								if ($_REQUEST['write'] == "true" || $_REQUEST['write'] == true || $_REQUEST['write'] == 1 || $_REQUEST['write'] == "1") {
									$write = true;
								} else if ($_REQUEST['write'] == "false" || $_REQUEST['write'] == false || $_REQUEST['write'] == 0 || $_REQUEST['write'] == "0") {
									$write = false;
								} else {
									$return['value'] .= "Le paramètre write est incorrecte<br>";
									$continue = false;
								}
							}
							$show = true;
							if (isset($_REQUEST['show'])) {
								if ($_REQUEST['show'] == "true" || $_REQUEST['show'] == true || $_REQUEST['show'] == 1 || $_REQUEST['show'] == "1") {
									$show = true;
								} else {
									$show = false;
								}
							}
							$show_items = false;
							$show_consumables = false;
							$show_ressources = false;
							if (isset($_REQUEST['showitems'])) {
								if ($_REQUEST['showitems'] == "true" || $_REQUEST['showitems'] == true || $_REQUEST['showitems'] == 1 || $_REQUEST['showitems'] == "1") {
									$show_items = true;
								} else {
									$show_items = false;
								}
							}
							if (isset($_REQUEST['showconsumables'])) {
								if ($_REQUEST['showconsumables'] == "true" || $_REQUEST['showconsumables'] == true || $_REQUEST['showconsumables'] == 1 || $_REQUEST['showconsumables'] == "1") {
									$show_consumables = true;
								} else {
									$show_consumables = false;
								}
							}
							if (isset($_REQUEST['showressources'])) {
								if ($_REQUEST['showressources'] == "true" || $_REQUEST['showressources'] == true || $_REQUEST['showressources'] == 1 || $_REQUEST['showressources'] == "1") {
									$show_ressources = true;
								} else {
									$show_ressources = false;
								}
							}

						if (!$continue) {
							$return['value'] .= "Les paramètres sont incorrects<br>";
						} else {

							// try {
								// Récupération des données
									$content = @file_get_contents($url);
									if($content === false){
										$return['value'] .= "Erreur lors de la récupération des données<br>";
										$continue = false;
									} else {
		
										$content = json_decode($content, true);
										$total_object = $content['total'];
			
										if ($offset > $total_object) {
											$return['value'] .= "L'offset est supérieur au nombre total d'objet<br>";
											$continue = false;
										} else {
											if ($offset + $limit > $total_object) {
												$limit = $total_object - $offset;
											}
			
											$return['value'] .= "Il y a " . $total_object . " objets<br>";
			
											// Récupération de la version de Dofus
												$dofus_version = @file_get_contents("https://api.dofusdb.fr/version?lang=fr");
												if($dofus_version === false){
													$dofus_version = "2.x";
												}
			
											// Traitement des données
												$objects = $content['data'];
												foreach ($objects as $object) {
													if (isset($object['name']['fr'])) {

														switch ($_REQUEST['template']) {

															// Traitement des ITEMS
																case self::TEMPLATE_ITEMS:
																	$extract = $this->extractItem($object, $dofus_version, $write);
																	if($extract['state']){
																		if (
																			$show &&
																			$show_items == true && get_class($extract['obj']) == "Item" ||
																			$show_consumables == true && get_class($extract['obj']) == "Consumable" ||
																			$show_ressources == true && get_class($extract['obj']) == "Ressource"
																		) {
																			ob_start(); ?>
																			<div class="m-4">
																				<p><?= $extract['obj']->getName() ?> - Niv. <?= $extract['obj']->getLevel() ?> <img width='30px' src='<?= $extract['img']?>'></p>
																				<p><?= isset($extract['display']['category']) ? $extract['display']['category'] : "" ?></p>
																				<p><?= isset($extract['display']['recipe']) ? $extract['display']['recipe'] : "" ?></p>
																				<p><?= isset($extract['display']['effects']) ? $extract['display']['effects'] : "" ?></p>
																				<p><?= isset($extract['display']['creation']) ? $extract['display']['creation'] : "" ?></p>
																			</div>
																			<?php $return['value'] .= ob_get_clean();
																		}
																	}
																break;
																
															default:
																$return['value'] .= "Le template est incorrect";
															break;
														}
													}
												}
	
											$return['token'] = $this->generateAndSaveToken();
											$return["state"] = true;
										}
									}
									
							// } catch (\Throwable $th) {
							// 	$return["value"] = "Erreur lors de la récupération des objets : " . $th->getMessage();
							// }
						}
					} else {
						$return["value"] = "Le template est manquant";
					}
				} else {
					$return["value"] = "Le token est invalide";
				}
			} else {
				$return["value"] = "Le token est invalide";
			}
		} else {
			$return["value"] = "Le token est manquant";
		}

		echo json_encode($return);
		flush();
	}

	// Extraction des Items, Consommables et Ressources
		private function extractItem($data, $dofus_version, $write = false): array {
			$manager = new ItemManager();

			$object_type = "Inconnu";
			$manager = null;
			$display = [];

			// Définition du Type
				$category_dbdofus = isset($data['typeId']) ? $data['typeId'] : -1;
				if (in_array($category_dbdofus, array_keys(self::VACUM_ITEMS_CATEGORY_TO_ITEM))) {
					$object_type = "Item";
					$manager = new ItemManager();
					$path_img = Item::FILES['logo']['dir'];
				} elseif (in_array($category_dbdofus, array_keys(self::VACUM_ITEMS_CATEGORY_TO_CONSOMABLE))) {
					$object_type = "Consommable";
					$manager = new ConsumableManager();
					$path_img = Consumable::FILES['logo']['dir'];
				} elseif (in_array($category_dbdofus, array_keys(self::VACUM_ITEMS_CATEGORY_TO_RESSOURCES))) {
					$object_type = "Ressource";
					$manager = new RessourceManager();
					$path_img = Ressource::FILES['logo']['dir'];
				} else {
					return [
						"state" => false,
						"obj" => null,
						"display" => "Type d'objet inconnu",
						'img' => ''
					];
				}

			// Identifiacation
				$official_id = isset($data['iconId']) ? $data['iconId'] : 0;
				$dofusdb_id = isset($data['id']) ? $data['id'] : 0;

			// Général
				$name = isset($data['name']['fr']) ? $data['name']['fr'] : "Inconnu";
				$description = isset($data['description']['fr']) ? $data['description']['fr'] : "";
				$level = isset($data['level']) ? $this->convertLevel($data['level']) : 0;
				$weight = isset($data['realWeight']) ? $data['realWeight'] : 0;
				$rarity = Item::RARITY_LIST['Commun'];
				
			// IMAGES
				$url_img = isset($data['imgset'][3]['url']) ? $data['imgset'][3]['url'] : "";
				$url_img_thumbnail = isset($data['imgset'][1]['url']) ? $data['imgset'][1]['url'] : "";

			// Category
				$new_category = -1;
				$supercategory = isset($data['type']['superTypeId']) ? $data['type']['superTypeId'] : "<span class='text-red'>-1</span>";
				$display['category'] = "Category DofusDB : .";
				$display['category'] .= isset($data['type']['superType']['name']['fr']) ? $data['type']['superType']['name']['fr'] . " (Super Catégorie : " . $supercategory . ")" : "<span class='text-red'>Aucune</span>" . " (Super Catégorie : " . $supercategory . ")";
				if ($object_type == "Ressource") {
					$new_category = self::VACUM_ITEMS_CATEGORY_TO_RESSOURCES[$category_dbdofus];
					$display['category'] .= ' | Categorie JDR - Ressource : ' . array_search(self::VACUM_ITEMS_CATEGORY_TO_RESSOURCES[$category_dbdofus], Ressource::TYPES);
				}
				if ($object_type == "Consommable") {
					$new_category = self::VACUM_ITEMS_CATEGORY_TO_CONSOMABLE[$category_dbdofus];
					$display['category'] .= ' | Categorie JDR - Consommable : ' . array_search(self::VACUM_ITEMS_CATEGORY_TO_CONSOMABLE[$category_dbdofus], Consumable::TYPES);
				}
				if ($object_type == "Item") {
					$new_category = self::VACUM_ITEMS_CATEGORY_TO_ITEM[$category_dbdofus];
					$display['category'] .= ' | Categorie JDR - Item : ' . array_search(self::VACUM_ITEMS_CATEGORY_TO_ITEM[$category_dbdofus], Item::TYPES);
				}

			// RECIPE
				$recipe = [];
				$display['recipe'] = "";
				$url_recipe = "https://api.dofusdb.fr/recipes/" . $dofusdb_id . "?lang=fr";
				$content_recipe = @file_get_contents($url_recipe);
				if($content_recipe !== false){
					$content_recipe = json_decode($content_recipe, true);
					if (!empty($content_recipe)) {
						if (isset($content_recipe['ingredients']) && $content_recipe['ingredientIds'] && isset($content_recipe['quantities'])) {
							if(is_array($content_recipe['ingredients']) && !empty($content_recipe['ingredients'])){
								$display['recipe'] = "<p>Recette : ";
								foreach ($content_recipe['ingredients'] as $ingredient) {
									if(is_array($ingredient) && !empty($ingredient)){
										$key = array_search($ingredient['id'], $content_recipe['ingredientIds']);
										$quantity = isset($content_recipe['quantities'][$key]) ? $content_recipe['quantities'][$key] : 1;
										$ress = $this->extractRessource($ingredient, $dofus_version);
										if ($ress["state"]) {
											$recipe[] = [
												"quantity" => $quantity,
												"ressource" => $ress['ressource'],
												'img' => $ress['img'],
											];
											$display['recipe'] .= $ress['display'];
										}
									}
								}
								$display['recipe'] .= "</p>";
							}
						}
					}
				}

			// EFFECTS
				$bonus = [];
				$effect = "";
				if (isset($data['effects'])) {
					if (is_array($data['effects']) && !empty($data['effects'])) {
						foreach ($data['effects'] as $eff) {
							if(is_array($eff) && !empty($eff)){
								$eff = $this->extractCharacteristic($eff);
								if ($eff["state"]) {
									$bonus[] = $eff['bonus'];
									$effect .= $eff['effect'];
									$display['effects'] = $eff['display'];
								}
							}
						}
					}
				}

			if ($manager->existsName($name) == false) { // L'objet n'existe pas dans la DB
				$display['creation'] = "<span style='color:red;'>N'existe pas</span>";

				$uniqid = uniqid();
				$obj = null;
				if ($object_type == "Item") {
					$obj = new Item([]);
				} elseif ($object_type == "Consommable") {
					$obj = new Consumable([]);
				} elseif ($object_type == "Ressource") {
					$obj = new Ressource([]);
				}

				// Création de l'objet et ajout si write = true
					// Ajout des informations générales
						$obj->setName($name);
						$obj->setUniqid($uniqid);
						$obj->setTimestamp_add();
						$obj->setTimestamp_updated();
						$obj->setOfficial_id($official_id);
						$obj->setDofusdb_id($dofusdb_id);
						$obj->setDofus_version($dofus_version);
						$obj->setType($new_category);
						$obj->setLevel($level);
						$obj->setDescription($description);
						$obj->setRarity($rarity);

					// Ajoutes des inormations spécifiques + upload des images des ressources si nécessaire
						switch ($object_type) {
							case 'Item': // Upload des images des ressources si besoins
								$obj->setEffect($effect);
								$obj->setBonus($bonus);
								if (!empty($recipe)) {
									foreach ($recipe as $rec) {
										if(is_array($rec['ressource']) && !empty($rec['ressource'])){
											$obj->setRessource($rec['ressource']);
											if($write == true && $rec['action'] == "add"){
												// Image
													$content = @file_get_contents($rec['img']['path']);
													if ($content !== false) {
														$path = Ressource::FILES['logo']['dir'] . $rec['ressource']->getUniqid() . ".png";
														if (!file_put_contents($path, $content)) {
															$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement de l'image</span>";
														}
													}
												// Thumbnail
													$content = @file_get_contents($rec['img']['thumbnail']);
													if ($content !== false) {
														$path = Ressource::FILES['logo']['dir'] . $rec['ressource']->getUniqid() . "_thumb.png";
														if (!file_put_contents($path, $content)) {
															$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement du thumbnail</span>";
														}
													}

												// FileManager::add(
												// 	path_read: $rec['img']['path'],
												// 	dir_dest: Ressource::FILES['logo']['dir'], 
												// 	rename: $rec['ressource']->getUniqid(), 
												// 	set_format_webp: true, 
												// 	resize: true, 
												// 	create_thumbnail: false,
												// 	is_thumbnail: false
												// );
												// FileManager::add(
												// 	path_read: $rec['img']['thumbnail'],
												// 	dir_dest: Ressource::FILES['logo']['dir'], 
												// 	rename: $rec['ressource']->getUniqid() . "_thumb", 
												// 	set_format_webp: true, 
												// 	resize: false, 
												// 	create_thumbnail: false,
												// 	is_thumbnail: true
												// );
											}
										}
									}
								}
							break;
							case 'Ressource':
								$obj->setWeight($weight);
							break;
						}
						
					// Ecritures
						if ($object_type != "Inconnu" && $write == true) {
							if ($manager->add($obj)) {
								// Image
									$content = @file_get_contents($url_img);
									if ($content !== false) {
										$path = $path_img . $obj->getUniqid() . ".png";
										if (!file_put_contents($path, $content)) {
											$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement de l'image</span>";
										}
									}
								// Thumbnail
									$content = @file_get_contents($url_img_thumbnail);
									if ($content !== false) {
										$path = $path_img . $obj->getUniqid() . "_thumb.png";
										if (!file_put_contents($path, $content)) {
											$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement du thumbnail</span>";
										}
									}
								// FileManager::add(
								// 	path_read: $url_img,
								// 	dir_dest: $path_img, 
								// 	rename: $obj->getUniqid(), 
								// 	set_format_webp: true, 
								// 	resize: false, 
								// 	create_thumbnail: false,
								// 	is_thumbnail: false
								// );
								// FileManager::add(
								// 	path_read: $url_img_thumbnail,
								// 	dir_dest: $path_img, 
								// 	rename: $obj->getUniqid() . "_thumb",  
								// 	set_format_webp: true, 
								// 	resize: false, 
								// 	create_thumbnail: false,
								// 	is_thumbnail: false
								// );
								$display['creation'] .= " <span class='strong' style='color:green;'>Ajouté</span>";
							} else {
								$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors de l'ajout</span>";
							}
						} else {
							$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors de la création</span>";
						}
						
			} else { // L'objet existe dans la DB - si il n'est pas utilisé, on le met à jour
				$display['creation'] = "<span style='color:green;'>Existe</span>";

				$obj = $manager->getFromName($name);
				$uniqid = $obj->getUniqid();

				if ($obj->getUsable() == false) {
					$display['creation'] .= " - <span style='color:red;'>non utilisé</span> - ";

					// Création de l'objet et ajout si write = true
						// Ajout des informations générales
							$obj->setTimestamp_updated();
							$obj->setOfficial_id($official_id);
							$obj->setDofusdb_id($dofusdb_id);
							$obj->setDofus_version($dofus_version);
							$obj->setType($new_category);
							$obj->setDescription($description);

						// Ajoutes des inormations spécifiques + upload des images des ressources si nécessaire
							switch ($object_type) {
								case 'Item':
									$obj->setBonus($bonus); // Bonus
									if (!empty($obj->getEffect())) { // Ajouter les effets. Si il y en a déjà, on les ajoute
										$effect = $obj->getEffect() . "<br> Effets 2.0 : " . $effect;
									} else {
										$effect = "Effets 2.0 : " . $effect;
									}
									$obj->setEffect($effect);
									if (!empty($recipe)) { // Ajouter les ressources
										foreach ($recipe as $rec) {
											if(is_array($rec['ressource']) && !empty($rec['ressource'])){
												$obj->setRessource($rec['ressource']);
												if($write == true && $rec['action'] == "add"){
													// Image
														$content = @file_get_contents($rec['img']['path']);
														if ($content !== false) {
															$path = Ressource::FILES['logo']['dir'] . $rec['ressource']->getUniqid() . ".png";
															if (!file_put_contents($path, $content)) {
																$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement de l'image</span>";
															}
														}
													// Thumbnail
														$content = @file_get_contents($rec['img']['thumbnail']);
														if ($content !== false) {
															$path = Ressource::FILES['logo']['dir'] . $rec['ressource']->getUniqid() . "_thumb.png";
															if (!file_put_contents($path, $content)) {
																$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement du thumbnail</span>";
															}
														}

													// FileManager::add(
													// 	path_read: $rec['img']['path'],
													// 	dir_dest: Ressource::FILES['logo']['dir'], 
													// 	rename: $rec['ressource']->getUniqid(), 
													// 	set_format_webp: true, 
													// 	resize: true, 
													// 	create_thumbnail: false,
													// 	is_thumbnail: false
													// );
													// FileManager::add(
													// 	path_read: $rec['img']['thumbnail'],
													// 	dir_dest: Ressource::FILES['logo']['dir'], 
													// 	rename: $rec['ressource']->getUniqid() . "_thumb", 
													// 	set_format_webp: true, 
													// 	resize: false, 
													// 	create_thumbnail: false,
													// 	is_thumbnail: true
													// );
												}
											}
										}
									}
								break;
								case 'Ressource':
									$obj->setWeight($weight);
								break;
							}

						// Ecritures
							if ($object_type != "Inconnu" && $write == true){
								if ($manager->update($obj)) {
									if(FileManager::remove($obj->getFile('logo', new Style(["display" => Content::FORMAT_BRUT]))) == false) {
										$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors de la suppression de l'image</span> - ";
									}
									// Image
										$content = @file_get_contents($url_img);
										if ($content !== false) {
											$path = $path_img . $obj->getUniqid() . ".png";
											if (!file_put_contents($path, $content)) {
												$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement de l'image</span>";
											}
										}
									// Thumbnail
										$content = @file_get_contents($url_img_thumbnail);
										if ($content !== false) {
											$path = $path_img . $obj->getUniqid() . "_thumb.png";
											if (!file_put_contents($path, $content)) {
												$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement du thumbnail</span>";
											}
										}

									// FileManager::add(
									// 	path_read: $url_img,
									// 	dir_dest: $path_img, 
									// 	rename: $obj->getUniqid(), 
									// 	set_format_webp: true, 
									// 	resize: true, 
									// 	create_thumbnail: false,
									// 	is_thumbnail: false
									// );
									// FileManager::add(
									// 	path_read: $url_img_thumbnail,
									// 	dir_dest: $path_img, 
									// 	rename: $obj->getUniqid() . "_thumb", 
									// 	set_format_webp: true, 
									// 	resize: false, 
									// 	create_thumbnail: false,
									// 	is_thumbnail: true
									// );
									$display['creation'] .= " <span class='strong' style='color:green;'>Mis à jour</span>";
								} else {
									$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors de la mise à jour</span>";
								}
							}
				} else {
					$display['creation'] .= " - <span style='color:green;'>utilisé</span>";
				}
			}

			// Suppression des fichiers résiduels
				// FileManager::clearTemp();

			return [
				'state' => true,
				'obj' => $obj,
				"display" => $display,
				"img" => $url_img_thumbnail
			];
		}

	// Extraction des sports
		private function extractSpell($data, $dofus_version, $write = false): array {
			$manager = new SpellManager();
			$display = [];
			$class_spell_id_dofusdb = $this->getIdDofusDBSpellFromClass();
			if(!is_array($class_spell_id_dofusdb)){$class_spell_id_dofusdb = [];}

			// Identifiacation
				$official_id = isset($data['iconId']) ? $data['iconId'] : 0;
				$dofusdb_id = isset($data['id']) ? $data['id'] : 0;

			// Général
				$name = isset($data['name']['fr']) ? $data['name']['fr'] : "Inconnu";
				$description = isset($data['description']['fr']) ? $data['description']['fr'] : "";
				if(str_contains($description, "[UNKNOWN_TEXT_ID_")){
					$description = "";
				}

				$category = in_array($dofusdb_id, $class_spell_id_dofusdb) ? Spell::CATEGORY_CLASS : Spell::CATEGORY_MOB;

			// IMAGES
				$url_img = isset($data['img']) ? $data['img'] : "";

			// AUTRES INFORMATIONS
				$effect = [];
				$level = [];
				$pa=[];
				$min_po = [];
				$max_po = [];
				$po_editable= [];
				$area = [];
				$cast_per_turn=[];
				$cast_per_target=[];
				$sight_line=[];
				$number_between_two_cast=[];
				$element = [];
				$is_magic = true;

				$grades = isset($data['spellLevels']) ? $data['spellLevels'] : [];
				if(!empty($grades) && is_array($grades)){
					// Récupération des informations de chaque grade
						foreach ($grades as $grade) {
							if(!empty($grade)){
								$url_grade = "https://api.dofusdb.fr/spell-levels/" . $grade . "?lang=fr";
								$grade_details = @file_get_contents($url_grade);
								if($grade_details !== false){
									$grade_details = json_decode($grade_details, true);
									if (!empty($grade_details)) {
										$level[] = isset($grade_details['minPlayerLevel']) ? (int) $grade_details['minPlayerLevel'] : 1;
										$pa[] = isset($grade_details['apCost']) ? (int) $grade_details['apCost'] : 0;
										$min_po[] = isset($grade_details['minRange']) ? (int) $grade_details['minRange'] : 0;
										$max_po[] = isset($grade_details['range']) ? (int) $grade_details['range'] : 0;
										$po_editable[] = isset($grade_details['rangeCanBeBoosted']) ? $grade_details['rangeCanBeBoosted'] : false;
										$cast_per_turn[] = isset($grade_details['maxCastPerTurn']) ? (int) $grade_details['maxCastPerTurn'] : 0;
										$cast_per_target[] = isset($grade_details['maxCastPerTarget']) ? (int) $grade_details['maxCastPerTarget'] : 0;
										$number_between_two_cast[] = isset($grade_details['minCastInterval']) ? (int) $grade_details['minCastInterval'] : 0;
										$sight_line[] = isset($grade_details['needVisibleEntity']) ? $grade_details['needVisibleEntity'] : true;

										// .......... A CONTINUER

									}
								}
							}
						}
					// Traitement des informations
						$level = is_array($level) ? $this->convertLevel(array_sum($level) / count($level)) : $this->convertLevel($level);
						$pa = is_array($pa) ? round(array_sum($pa) / count($pa)) : $pa;

						// .......... A CONTINUER
	
				}

			if ($manager->existsName($name) == false) { // L'objet n'existe pas dans la DB
				$display['creation'] = "<span style='color:red;'>N'existe pas</span>";

				$uniqid = uniqid();
				$obj = new Spell([]);
				
				// Création de l'objet et ajout si write = true
					// Ajout des informations générales
						$obj->setName($name);
						$obj->setUniqid($uniqid);
						$obj->setTimestamp_add();
						$obj->setTimestamp_updated();
						$obj->setOfficial_id($official_id);
						$obj->setDofusdb_id($dofusdb_id);
						$obj->setDofus_version($dofus_version);
						$obj->setLevel($level);
						$obj->setDescription($description);
						$obj->setCategory($category);

						$obj->setIs_magic($is_magic);

						// .......... A CONTINUER

					// Ecritures
						if ($write == true) {
							if ($manager->add($obj)) {
								// Image
									$content = @file_get_contents($url_img);
									if ($content !== false) {
										$path = Spell::FILES['logo']['dir'] . $obj->getUniqid() . ".png";
										if (!file_put_contents($path, $content)) {
											$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement de l'image</span>";
										}
									}
								$display['creation'] .= " <span class='strong' style='color:green;'>Ajouté</span>";
							} else {
								$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors de l'ajout</span>";
							}
						} else {
							$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors de la création</span>";
						}
						
			} else { // L'objet existe dans la DB - si il n'est pas utilisé, on le met à jour
				$display['creation'] = "<span style='color:green;'>Existe</span>";

				$obj = $manager->getFromName($name);
				$uniqid = $obj->getUniqid();

				if ($obj->getUsable() == false) {
					$display['creation'] .= " - <span style='color:red;'>non utilisé</span> - ";

					// Création de l'objet et ajout si write = true
						// Ajout des informations générales
							$obj->setTimestamp_updated();
							$obj->setOfficial_id($official_id);
							$obj->setDofusdb_id($dofusdb_id);
							$obj->setDofus_version($dofus_version);
							$obj->setDescription($description);

							// .......... A CONTINUER

						// Ecritures
							if ($write == true){
								if ($manager->update($obj)) {
									if(FileManager::remove($obj->getFile('logo', new Style(["display" => Content::FORMAT_BRUT]))) == false) {
										$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors de la suppression de l'image</span> - ";
									}
									// Image
										$content = @file_get_contents($url_img);
										if ($content !== false) {
											$path = Spell::FILES['logo']['dir'] . $obj->getUniqid() . ".png";
											if (!file_put_contents($path, $content)) {
												$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors du téléchargement de l'image</span>";
											}
										}

									$display['creation'] .= " <span class='strong' style='color:green;'>Mis à jour</span>";
								} else {
									$display['creation'] .= " <span class='strong' style='color:red;'>Erreur lors de la mise à jour</span>";
								}
							}
				} else {
					$display['creation'] .= " - <span style='color:green;'>utilisé</span>";
				}
			}

			return [
				'state' => true,
				'obj' => $obj,
				"display" => $display,
				"img" => $url_img
			];
		}

	// Extractions secondaires
		private function extractCharacteristic($data){
			$display = "";
			if (isset($data['category']) && isset($data['characteristic']) && isset($data['from']) && isset($data['to'])) {
				$caracteristic_name = "<span class='text-red bold'>Inconnu</span>";
				if (in_array($data['characteristic'], array_keys(self::VACUM_ITEMS_CARACTERISTICS_ID))) {
					$caracteristic_name = "<span class='text-green'>" . self::VACUM_ITEMS_CARACTERISTICS_ID[$data['characteristic']] . "</span>";
				}
				$display = $data['category'] . ' - ' . $data['characteristic'] . " (" . $caracteristic_name . ") : " . $data['from'] . " to " . $data["to"] . ") |<br>";

				$value = $data['to'] - $data['from'] > 0 ? ($data['from'] + $data['to']) / 2 : $data['from'];
				$bonus = [];
				$effect = '';

				if ($data['characteristic'] == array_search(Creature::CARACTERISTICS['life']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT)) {
					$value = $this->convertLife($value);
				} elseif (in_array(
					$data['characteristic'],
					[
						array_search(Creature::CARACTERISTICS['vitality']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['sagesse']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['force']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['intel']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['agi']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['chance']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT)
					]
				)) {
					$value = $this->convertStat($value);
				} elseif (in_array(
					$data['characteristic'],
					[
						array_search(Creature::CARACTERISTICS['res_neutre']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['res_terre']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['res_feu']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['res_eau']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['res_air']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT)
					]
				)) {
					$value = $this->convertRes($value);
				} elseif (in_array(
					$data['characteristic'],
					[
						array_search(Creature::CARACTERISTICS['do_fixe_neutre']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['do_fixe_terre']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['do_fixe_feu']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['do_fixe_eau']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['do_fixe_air']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['do_fixe_multiple']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT)
					]
				)) {
					$value = $this->convertDamage_fixe($value);
				} elseif (in_array(
					$data['characteristic'],
					[
						array_search(Creature::CARACTERISTICS['dodge_pa']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT),
						array_search(Creature::CARACTERISTICS['dodge_pm']['name'], self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT)
					]
				)) {
					$value = $this->convertDodge($value);
				}

				if (in_array($data['characteristic'], array_keys(self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT))) {
					$bonus = [
						'type' => self::VACUM_ITEMS_CARACTERISTICS_TO_OWN_CARACT[$data['characteristic']],
						'value' => $value
					];
				} else {
					$effect = $caracteristic_name . " : de " . $data['from'] . " à " . $data["to"] . " (Cette caractéristique est directement issu du jeu Dofus)<br>";
				}
				return [
					'state' => true,
					'display' => $display,
					'bonus' => $bonus,
					'effect' => $effect
				];
			} else {
				return [
					'state' => false
				];
			}
		}
		private function extractRessource($data, $dofus_version){
			$manager = new RessourceManager();
			$display = null;
			$ressource = null;

			$uniqid = "";

			if($manager->existsDofusdb_id($data['id'])) {
				$action = "nothing";
				$ressource = $manager->getFromDofusdb_id($data['id']);
				$img = [
					'path' => $ressource->getFile(name_file : 'logo', style : new Style(["display" => Content::FORMAT_BRUT])),
					'thumbnail' => $ressource->getFile(name_file : 'logo', style : new Style(["display" => Content::FORMAT_BRUT]), getThumbnail: true)
				];
			} else {
				$action = "add";
				$recipe_type = isset($data['typeId']) && isset(self::VACUM_ITEMS_CATEGORY_TO_RESSOURCES[$data['typeId']]) ? self::VACUM_ITEMS_CATEGORY_TO_RESSOURCES[$data['typeId']] : Ressource::TYPE_RESSOURCES_DIV;
				$uniqid = uniqid();
				$ressource = new Ressource([
					'uniqid' => $uniqid,
					'official_id' => isset($data['iconId']) ? $data['iconId'] : 0,
					'dofusdb_id' => $data['id'],
					"dofus_version" => $dofus_version,
					'name' => isset($data['name']['fr']) ? $data['name']['fr'] : "Inconnu " . $uniqid,
					'level' => isset($data['level']) ? $this->convertLevel($data['level']) : 0,
					'description' => isset($data['description']['fr']) ? $data['description']['fr'] : "",
					'type' => $recipe_type,
					'weight' => isset($data['realWeight']) ? $data['realWeight'] : 0
				]);
				$ressource->setTimestamp_add(time());
				$ressource->setTimestamp_updated(time());
				$img = [
					'path' => isset($data['imgset'][3]['url']) ? $data['imgset'][3]['url'] : "",
					'thumbnail' => isset($data['imgset'][1]['url']) ? $data['imgset'][1]['url'] : ""
				];
			}
		
			ob_start(); ?>
				<span><?= $ressource->getName() ?> <img width='20px' src='<?= $img['thumbnail'] ?>'></span>
			<?php $display = ob_get_clean();
				
			return [
				'state' => true,
				'action' => $action,
				'ressource' => $ressource,
				'img' => $img,
				'display' => $display
			];
		}
		private function getIdDofusDBSpellFromClass() : array {
			$spell_id = [];
			$offset = 0;
			$total = null;
			$url = self::VACUM_CLASS_URL . '&$skip=' . $offset;
			$content = @file_get_contents($url);
			if($content !== false){
				$content = json_decode($content, true);
				$total = $content['total'];
				$class = $content['data'];
				foreach ($class as $cl) {
					if(isset($cl['breedSpellsId'])){
						if(is_array($cl['breedSpellsId']) && !empty($cl['breedSpellsId'])){
							foreach ($cl['breedSpellsId'] as $id) {
								$spell_id[] = $id;
							}
						}
					}
				}
				while(count($spell_id) < $total){
					$offset += 10;
					$url = self::VACUM_CLASS_URL . '&$skip=' . $offset;
					$content = @file_get_contents($url);
					if($content !== false){
						$content = json_decode($content, true);
						$class = $content['data'];
						foreach ($class as $cl) {
							if(isset($cl['breedSpellsId'])){
								if(is_array($cl['breedSpellsId']) && !empty($cl['breedSpellsId'])){
									foreach ($cl['breedSpellsId'] as $id) {
										$spell_id[] = $id;
									}
								}
							}
						}
					}
				}

			}
			return $spell_id;
		}

	public function cleanImage(){
		$return = [
			'state' => false,
			'value' => "<p>"
		];

		$headers = getallheaders();
		$token = str_replace('Bearer ', '', $headers['Authorization']);
		if (isset($token)) {
			if ($this->isTokenValid($token)) {

				$list_manager = [
					[
						'manager' => new ItemManager(),
						'path' => "medias/modules/items/",
						'name' => 'logo',
						"trash_name" => "items"
					],
					[
						"manager" => new MobManager(),
						"path" => "medias/modules/mobs/",
						"name" => "logo",
						"trash_name" => "mobs"
					],
					[
						"manager" => new SpellManager(),
						"path" => "medias/modules/spells/",
						"name" => "logo",
						"trash_name" => "spells"
					],
					[
						"manager" => new ConsumableManager(),
						"path" => "medias/modules/consumables/",
						"name" => "logo",
						"trash_name" => "consumables"
					],
					[
						"manager" => new RessourceManager(),
						"path" => "medias/modules/ressources/",
						"name" => "logo",
						"trash_name" => "ressources"
					],
					[
						"manager" => new CapabilityManager(),
						"path" => "medias/modules/capabilities/",
						"name" => "logo",
						"trash_name" => "capabilities"
					],
					[
						"manager" => new ConditionManager(),
						"path" => "medias/modules/conditions/",
						"name" => "icon",
						"trash_name" => "conditions"
					],
					[
						"manager" => new Mob_raceManager(),
						"path" => "medias/modules/races/",
						"name" => "logo",
						"trash_name" => "races"
					],
					[
						"manager" => new NpcManager(),
						"path" => "medias/modules/npc/",
						"name" => "logo",
						"trash_name" => "npc"
					],
				];
				$n = 0;
				foreach ($list_manager as $manager) {
					$obj = $manager["manager"]->getAll();
					$path_obj = [];
					foreach ($obj as $item) {
						$path_obj[] = $item->getFile($manager['name'], new Style(["display" => Content::FORMAT_BRUT]));
					}
					$files = glob($manager["path"] . "*");
					foreach ($files as $file) {
						if (FileManager::isFile($file)) {
							if (!in_array($file, $path_obj)) {
								$path_trash = self::TRASH_PATH . "/" . $manager['trash_name'] . '/' . basename($file);
								FileManager::move($file, $path_trash);
								$n++;
								$return["value"] .= "Le fichier " . $file . " a été déplacé dans la corbeille<br><img width='100px' src='" . $path_trash . "'><br><br>";
							}
						}
					}
				}
				$return['state'] = true;
				$return["value"] .= "</p><p>Il y a " . $n . " fichier(s) qui ont été déplacé(s) dans la corbeille</p>";
			} else {
				$return["value"] = "Le token est invalide";
			}
		} else {
			$return["value"] = "Le token est invalide";
		}

		echo json_encode($return);
		flush();
	}

	// Fichier à mettre en cache
		public function getListFileToBeCache() {
			$directories = ['/src/css', '/src/js', '/medias'];
			$allFiles = [];
	
			foreach ($directories as $dir) {
				$allFiles = array_merge($allFiles, $this->getFiles($_SERVER['DOCUMENT_ROOT'] . $dir));
			}
	
			header('Content-Type: application/json');
			echo json_encode($allFiles);
		}
	
		private function getFiles($dir, &$results = array()) {
			$files = scandir($dir);
			foreach ($files as $file) {
				$path = realpath($dir . DIRECTORY_SEPARATOR . $file);
				if (!is_dir($path)) {
					$results[] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
				} else if ($file != "." && $file != "..") {
					$this->getFiles($path, $results);
				}
			}
			return $results;
		}
	
}
