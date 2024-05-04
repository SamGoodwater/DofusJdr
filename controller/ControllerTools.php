<?php
    $_SESSION['super_category_text'] = [];
    $_SESSION['category_text'] = [];

class ControllerTools extends Controller{

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
    if(is_array($files) && !empty($files)){
        $time_last_backup = filemtime(max($files)); // le dernier fichier de sauvegarde
    }
    $headers = getallheaders();
    $token = str_replace('Bearer ', '', $headers['Authorization']);

    if (time() - $time_last_backup >= $timing_allow_backup * 60) {
      if(isset($token)) {
        if($this->isTokenValid($token)) {

            $backup_path = dirname(__FILE__) . "/../backup/db/";
            $backup_path = str_replace("\\", "/", $backup_path);
        
            // check if mysqldump is available
            $output = array();
            exec('which mysqldump', $output);
            if(!empty($output)) {
                // mysqldump is available use it
                $text = "Les bases de données ont bien été enregistrées via mysqldump";
                exec("mysqldump --user={$GLOBALS["pdoLogin"]} --password={$GLOBALS["pdoPassword"]} {$GLOBALS["pdoName"]} > {$backup_path}{$GLOBALS["pdoName"]}_" . date("Y-m-d-H-i-s") . ".sql");
            } else {
                // mysqldump is not available use SELECT INTO OUTFILE
                try {
                    $pdo = new PDO("mysql:host=".$GLOBALS['pdoHost'].";dbname=".$GLOBALS['pdoName']."", $GLOBALS["pdoLogin"], $GLOBALS["pdoPassword"]);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt = $pdo->query("SHOW TABLES");
                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($tables as $table) {
                        $file = $backup_path . $GLOBALS["pdoName"] .'_' . $table . '_' . date("Y-m-d-H-i-s") . ".sql";
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
                if (is_file($file)) {
                    if ($now - filemtime($file) >= $days_keeping_db * 24 * 60 * 60) {
                        unlink($file);
                        $n++;
                    }
                }
            }

          ob_start(); ?>
            <div class='text-left'>
              <p><?=$text?></p>
              <p>Il y a <?=$n?> ancienne(s) base(s) de donnée(s) qui ont/a été supprimé</p>
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
    if(isset($token)) {
      if($this->isTokenValid($token)) {

        $manager = new PageManager();
        $managerS = new SectionManager();
        $n = false;
        foreach (Page::UNIQID_NO_EDIT as $name => $uniqid) {
          if(!$manager->existsUniqid($uniqid)){
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
            if(!empty($name_page)){
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
              
              if(isset($section)){
                $section->setUniqid_page($page->getUniqid());
                $managerS->add($section);
              }
            }
          }
        }
        $return["state"] = true;
        $return["value"] .= "</p>";
        if(!$n){
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

  const VACUM_URL = 'https://api.dofusdb.fr/items?typeId%5B$ne%5D=203&$sort=level&lang=fr';

  const VACUM_CATEGORY = [
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
  const VACUM_CATEGORY_TO_RESSOURCES = [
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
  const VACUM_CATEGORY_TO_CONSOMABLE = [
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
  const VACUM_CATEGORY_TO_ITEM = [
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

  const VACUM_SUPER_CATEGORY = [
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
  const VACUM_CARACTERISTICS_ID = [
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
  const VACUM_CARACTERISTICS_TO_OWN_CARACT = [
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

  public function getTotalElementFromDofusDB(){
    $return = [
      'state' => false,
      'value' => ""
    ];

    $headers = getallheaders();
    $token = str_replace('Bearer ', '', $headers['Authorization']);

    if(isset($token)) {
      if($this->isTokenValid($token)) {

        try {
          $content = file_get_contents(self::VACUM_URL . '&$limit=0');
          $content = json_decode($content, true);
          $return["value"] = $content['total'];
          $return["state"] = true;
        } catch (\Throwable $th) {
          $return["value"] = "Erreur lors de la récupération du nombre total d'objet";
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

    $managerRessource = new RessourceManager();

    $headers = getallheaders();
    $token = str_replace('Bearer ', '', $headers['Authorization']);

    if(isset($token)) {
      if($this->isTokenValid($token)) {
        $continue = true;

        if(isset($_REQUEST['offset']) && isset($_REQUEST['limit'])){
          $offset = $_REQUEST['offset'];
          $limit = $_REQUEST['limit'];
        } else {
          $return['value'] .= "L'offset ou la limite est incorrecte<br>";
          $continue = false;
        }
        
        $write = false;
        if(isset($_REQUEST['write'])){
          if($_REQUEST['write'] == "true" || $_REQUEST['write'] == true || $_REQUEST['write'] == 1 || $_REQUEST['write'] == "1"){
            $write = true;
          } else if($_REQUEST['write'] == "false" || $_REQUEST['write'] == false || $_REQUEST['write'] == 0 || $_REQUEST['write'] == "0"){
            $write = false;
          } else {
            $return['value'] .= "Le paramètre write est incorrecte<br>";
            $continue = false;
          }
        }

        if(!$continue){
          $return['value'] .= "Les paramètres sont incorrects<br>";
        }else{
          
          $url = self::VACUM_URL;
          if($limit > 0 && isset($url)){
            $url .= '&$skip='.$offset.'&$limit='.$limit;
            $return['value'] .= "API URL : " . $url . '<br>';

            $content = file_get_contents($url); 
            $content = json_decode($content, true);

            $total_object = $content['total'];

            if($offset > $total_object ){
              $return['value'] .= "L'offset est supérieur au nombre total d'objet<br>";
              $continue = false;
            } else {
              if($offset + $limit > $total_object){
                $limit = $total_object - $offset;
              }

              $return['value'] .= "Il y a " . $total_object . " objets<br>";

              try {
                $dofus_version = file_get_contents("https://api.dofusdb.fr/version?lang=fr");
              } catch (\Throwable $th) {
                $dofus_version = "2.x";
              }

              $content = file_get_contents($url);
              $content = json_decode($content, true);
              $objects = $content['data'];

              foreach ($objects AS $object) {
                  if(isset($object['name']['fr'])){

                      $official_id = $object['iconId'];
                      $dofusdb_id = $object['id'];
                      $name = $object['name']['fr'];
                      $description = $object['description']['fr'];
                      $level = isset($object['level']) ? $this->convertLevel($object['level']) : 0;

                      $category = isset($object['typeId']) ? $object['typeId'] : -1;
                      $category_name = isset($object['type']['name']['fr']) ? $object['type']['name']['fr'] : "Aucune";
                      $supercategory = isset($object['type']['superTypeId']) ? $object['type']['superTypeId'] : "<span class='text-red'>-1</span>";
                      $supercategory_name = isset($object['type']['superType']['name']['fr']) ? $object['type']['superType']['name']['fr'] : "<span class='text-red'>Aucune</span>";

                      $weight = isset($object['realWeight']) ? $object['realWeight'] : 0;

                      $object_type = "Inconnu";
                      $manager = null;
                      if(in_array($category, array_keys(self::VACUM_CATEGORY_TO_ITEM))){
                        $object_type = "Item";
                        $manager = new ItemManager();
                        $path_img = Item::FILES['logo']['dir'];
                      } elseif(in_array($category, array_keys(self::VACUM_CATEGORY_TO_CONSOMABLE))){
                        $object_type = "Consommable";
                        $manager = new ConsumableManager();
                        $path_img = Consumable::FILES['logo']['dir'];
                      } elseif(in_array($category, array_keys(self::VACUM_CATEGORY_TO_RESSOURCES))){
                        $object_type = "Ressource";
                        $manager = new RessourceManager();
                        $path_img = Ressource::FILES['logo']['dir'];
                      } else {
                        $object_type = "Inconnu";
                      }

                      $recipe = [];
                      $effect_text = "";
                      $bonus = [];
                      $effect = "";
                      $rarity = Item::RARITY_LIST['Commun'];
                      $creation_text = "";

                      if($object_type != "Inconnu"){

                        if($object_type == "Ressource"){
                          $new_category = self::VACUM_CATEGORY_TO_RESSOURCES[$category];	
                          $new_category_text = 'Ressource : ' . array_search(self::VACUM_CATEGORY_TO_RESSOURCES[$category],Ressource::TYPES);
                        }
                        if($object_type == "Consommable"){
                          $new_category = self::VACUM_CATEGORY_TO_CONSOMABLE[$category];
                          $new_category_text = 'Consommable : ' . array_search(self::VACUM_CATEGORY_TO_CONSOMABLE[$category],Consumable::TYPES);
                        }
                        if($object_type == "Item"){
                          $new_category = self::VACUM_CATEGORY_TO_ITEM[$category];
                          $new_category_text = 'Item : ' . array_search(self::VACUM_CATEGORY_TO_ITEM[$category],Item::TYPES);
                        }
  
                        try {
                          $url_recipe = "https://api.dofusdb.fr/recipes/".$dofusdb_id."?lang=fr";
                          $content_recipe = @file_get_contents($url_recipe);
                          $content_recipe = json_decode($content_recipe, true);
                          if(!empty($content_recipe)){
                            if(isset($content_recipe['ingredientIds']) && isset($content_recipe['quantities'])){
                              foreach ($content_recipe['ingredientIds'] AS $key => $ingredient) {
                                if(isset($content_recipe['quantities'][$key])){
                                  $quantity = $content_recipe['quantities'][$key] > 1 ? $content_recipe['quantities'][$key] : 1;
                                  $uniqid = "";
                                  if($managerRessource->existsDofusdb_id($ingredient)){
                                    $ressource = $managerRessource->getFromDofusdb_id($ingredient);
                                    $uniqid = $ressource->getUniqid();
                                  } else {
                                    $content_ressource = $content_recipe['ingredients'][$key];
                                    $recipe_type = isset($content_ressource['typeId']) && isset(self::VACUM_CATEGORY_TO_RESSOURCES[$content_ressource['typeId']]) ? self::VACUM_CATEGORY_TO_RESSOURCES[$content_ressource['typeId']] : Ressource::TYPE_RESSOURCES_DIV;
                                    $uniqid = uniqid();
                                    $ressource = new Ressource([
                                      'uniqid' => $uniqid,
                                      'official_id' => isset($content_ressource['iconId']) ? $content_ressource['iconId'] : 0,
                                      'dofusdb_id' => $ingredient,
                                      'name' => isset($content_ressource['name']['fr']) ? $content_ressource['name']['fr'] : "Inconnu " . $uniqid,
                                      'level' => isset($content_ressource['level']) ? $this->convertLevel($content_ressource['level']) : 0,
                                      'description' => isset($content_ressource['description']['fr']) ? $content_ressource['description']['fr'] : "",
                                      'type' => $recipe_type,
                                      'weight' => isset($content_ressource['realWeight']) ? $content_ressource['realWeight'] : 0
                                    ]);
                                    $ressource->setTimestamp_add(time());
                                    $ressource->setTimestamp_updated(time());
                                    $managerRessource->add($ressource);
                                  }
                                  $recipe[] = [
                                    'uniqid' => $uniqid,
                                    'quantity' => $quantity,
                                    "action" => "add"
                                  ];
                                }
                              }
                            }
                          }
                        } catch (\Throwable $th) {
                          $recipe = [];
                        }
  
                        $url_img = $object['imgset'][3]['url'];
                        $url_img_thumbnail = $object['imgset'][1]['url'];

                        if(isset($object['effects'])){
                            if(is_array($object['effects']) && !empty($object['effects'])){
                                foreach ($object['effects'] AS $eff) {
                                  if(isset($eff['category']) && isset($eff['characteristic']) && isset($eff['from']) && isset($eff['to'])){
                                    $caracteristic_name = "<span class='text-red bold'>Inconnu</span>"; 
                                    if(in_array($eff['characteristic'], array_keys(self::VACUM_CARACTERISTICS_ID))){
                                      $caracteristic_name = "<span class='text-green'>" . self::VACUM_CARACTERISTICS_ID[$eff['characteristic']]."</span>";
                                    }
                                    $effect_text .= $eff['category'] . ' - ' . $eff['characteristic'] ." (".$caracteristic_name.") : " . $eff['from'] . " to " . $eff["to"] . ") |<br>";
                                  
                                    $value = $eff['to'] - $eff['from'] > 0 ? ($eff['from'] + $eff['to']) / 2 : $eff['from'];
  
                                      if($eff['characteristic'] == array_search(Creature::CARACTERISTICS['life']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT)){
                                        $value = $this->convertLife($value);
                                      } elseif(in_array($eff['characteristic'], 
                                          [
                                            array_search(Creature::CARACTERISTICS['vitality']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT), 
                                            array_search(Creature::CARACTERISTICS['sagesse']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT), 
                                            array_search(Creature::CARACTERISTICS['force']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT), 
                                            array_search(Creature::CARACTERISTICS['intel']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT), 
                                            array_search(Creature::CARACTERISTICS['agi']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT), 
                                            array_search(Creature::CARACTERISTICS['chance']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT)
                                          ])) 
                                      {
                                          $value = $this->convertStat($value);
                                      } elseif(in_array($eff['characteristic'],
                                            [
                                              array_search(Creature::CARACTERISTICS['res_neutre']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['res_terre']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['res_feu']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['res_eau']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['res_air']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT)
                                            ]))
                                      {
                                        $value = $this->convertRes($value);
                                      } elseif(in_array($eff['characteristic'],
                                            [
                                              array_search(Creature::CARACTERISTICS['do_fixe_neutre']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['do_fixe_terre']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['do_fixe_feu']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['do_fixe_eau']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['do_fixe_air']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['do_fixe_multiple']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT)
                                            ]))
                                      {
                                        $value = $this->convertDamage_fixe($value);
                                      } elseif(in_array($eff['characteristic'],
                                            [
                                              array_search(Creature::CARACTERISTICS['dodge_pa']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT),
                                              array_search(Creature::CARACTERISTICS['dodge_pm']['name'], self::VACUM_CARACTERISTICS_TO_OWN_CARACT)
                                            ]))
                                      {
                                        $value = $this->convertDodge($value);
                                      }
  
                                    if(in_array($eff['characteristic'], array_keys(self::VACUM_CARACTERISTICS_TO_OWN_CARACT))){
                                      $bonus[] = [
                                        'type' => self::VACUM_CARACTERISTICS_TO_OWN_CARACT[$eff['characteristic']],
                                        'value' => $value
                                      ];
                                    } else {
                                      $effect .= $caracteristic_name . " : de " . $eff['from'] . " à " . $eff["to"] . " (Cette caractéristique est directement issu du jeu Dofus)<br>";
                                    }
  
                                  }
                                }
                            }
                        }
  
                        if($manager->existsName($name) == false){
                          $creation_text .= "<span style='color:red;'>N'existe pas</span>";
  
                            if($write == true){
                              $uniqid = uniqid();
                              $obj = null;
                              if($object_type == "Item"){
                                $obj = new Item([]);
                              } elseif($object_type == "Consommable"){
                                $obj = new Consumable([]);
                              } elseif($object_type == "Ressource"){
                                $obj = new Ressource([]);
                              }
  
                              // Général
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
  
                                switch ($object_type) {
                                  case 'Item':
                                    $obj->setEffect($effect);
                                    $obj->setBonus($bonus);
                                    if(!empty($recipe)){
                                      foreach ($recipe AS $rec) {
                                        $obj->setRessource($rec);
                                      }
                                    }
                                  break;
                                  case 'Consommable':
                                    
                                  break;
                                  case 'Ressource':
                                    $obj->setWeight($weight);
                                  break;
                                }
                                
                                if($object_type != "Inconnu"){
                                  if($manager->add($obj)){
                                    $creation_text .= " <span class='strong' style='color:green;'>Ajouté</span>";
                                  } else {
                                    $creation_text .= " <span class='strong' style='color:red;'>Erreur lors de l'ajout</span>";
                                  }
                                }
                            }
  
                        } else {
                          $creation_text .= "<span style='color:green;'>Existe</span>";
  
                            if($write == true){
                              $obj = $manager->getFromName($name);
  
                              if($obj->getUsable() == false){
                                $creation_text .= " - <span style='color:red;'>non utilisé</span>";
                              } else {
                                $creation_text .= " - <span style='color:green;'>utilisé</span>";
                              }
  
                              $uniqid = $obj->getUniqid();
                              if($obj->getUsable() == false){
  
                                $obj->setTimestamp_updated();
                                $obj->setOfficial_id($official_id);
                                $obj->setDofusdb_id($dofusdb_id);
                                $obj->setDofus_version($dofus_version);
                                $obj->setType($new_category);
                                $obj->setDescription($description);
  
                                if($object_type == 'Item'){
                                  $obj->setBonus($bonus);
  
                                  if(!empty($obj->getEffect())){
                                      $effect = $obj->getEffect() . "<br> Effets 2.0 : " . $effect;
                                  } else {
                                      $effect = "Effets 2.0 : " . $effect;
                                  }
                                  if(!empty($recipe)){
                                    foreach ($recipe AS $rec) {
                                      $obj->setRessource($rec);
                                    }
                                  }
                                  $obj->setEffect($effect);
                                  
                                } elseif($object_type == 'Ressource'){
                                  $obj->setWeight($weight);
                                }
  
                              }

                              if($object_type != "Inconnu"){
                                if($manager->update($obj)){
                                  $creation_text .= " <span class='strong' style='color:green;'>Mis à jour</span>";
                                } else {
                                  $creation_text .= " <span class='strong' style='color:red;'>Erreur lors de la mise à jour</span>";
                                }
  
                                if(FileManager::remove($obj->getFile('logo', new Style(["display" => Content::FORMAT_BRUT]))) == false){
                                  $creation_text .= " <span class='strong' style='color:red;'>Erreur lors de la suppression de l'image</span>";
                                }
                              }
                              
                            }
                        }

                        if($write == true && $object_type != "Inconnu"){
                          try {
                              $file = @file_get_contents($url_img);
                              if($file) {
                                @file_put_contents($path_img.$uniqid.".png", $file);
                              }
                          } catch (Exception $e) {
                              $creation_text .= "<span style='color:red;'>Erreur lors de la récupération de l'image</span>";
                          }
  
                          try {
                              $file_thum = @file_get_contents($url_img_thumbnail);
                              if ($file_thum) {
                                  @file_put_contents($path_img.$uniqid."_thumb.png", $file_thum);
                              }
                          } catch (Exception $e) {
                              $creation_text .= "<span style='color:red;'>Erreur lors de la récupération de l'image</span>";
                          }
                        }

                      }


                      if($supercategory != 14){
                        $new_category = '<span class="text-red bold">inconnu</span>';
                      } else {
                        $new_category = 'Non utilisé';
                      }
                  
                      if(!in_array($supercategory_name, $_SESSION['super_category_text'])){
                        $_SESSION['super_category_text'][$supercategory] = $supercategory_name;
                      }
                      if(!in_array($category_name, $_SESSION['category_text'])){
                        $_SESSION['category_text'][$category] = $category_name;
                      }

                      $exist_supercategory = '<i class="fa-solid text-red fa-xmark"></i>';
                      $exist_category = '<i class="fa-solid text-red fa-xmark"></i>';
                      if(in_array($supercategory, array_keys(self::VACUM_SUPER_CATEGORY))){
                        $exist_supercategory = '<i class="fa-solid text-green fa-check"></i>';
                      }
                      if(in_array($category, array_keys(self::VACUM_CATEGORY))){
                        $exist_category = '<i class="fa-solid text-green fa-check"></i>';
                      }

                      $show = true;
                      $show_items = false;
                      $show_consumables = false;
                      $show_ressources = false;
                      if(isset($_REQUEST['show'])){
                        if($_REQUEST['show'] == "true" || $_REQUEST['show'] == true || $_REQUEST['show'] == 1 || $_REQUEST['show'] == "1"){
                          $show = true;
                        } else {
                          $show = false;
                        }
                      }
                      if(isset($_REQUEST['showitems'])){
                        if($_REQUEST['showitems'] == "true" || $_REQUEST['showitems'] == true || $_REQUEST['showitems'] == 1 || $_REQUEST['showitems'] == "1"){
                          $show_items = true;
                        } else {
                          $show_items = false;
                        }
                      }
                      if(isset($_REQUEST['showconsumables'])){
                        if($_REQUEST['showconsumables'] == "true" || $_REQUEST['showconsumables'] == true || $_REQUEST['showconsumables'] == 1 || $_REQUEST['showconsumables'] == "1"){
                          $show_consumables = true;
                        } else {
                          $show_consumables = false;
                        }
                      }
                      if(isset($_REQUEST['showressources'])){
                        if($_REQUEST['showressources'] == "true" || $_REQUEST['showressources'] == true || $_REQUEST['showressources'] == 1 || $_REQUEST['showressources'] == "1"){
                          $show_ressources = true;
                        } else {
                          $show_ressources = false;
                        }
                      }

                      if(
                        $show &&
                        $show_items == true && in_array($category, array_keys(self::VACUM_CATEGORY_TO_ITEM)) ||
                        $show_consumables == true && in_array($category, array_keys(self::VACUM_CATEGORY_TO_CONSOMABLE)) ||
                        $show_ressources == true && in_array($category, array_keys(self::VACUM_CATEGORY_TO_RESSOURCES))
                      ){
                        ob_start(); ?>
                          <div class="m-4">
                            <p><?=$name?> - Niv. <?=$level?>  <img width='30px' src='<?=$url_img?>'></p>
                            <p>Catégorie du JDR : <?=$new_category_text?> | <?=$exist_supercategory?> Super Catégorie : <?=$supercategory?> - <?=$supercategory_name?> | <?=$exist_category?> Catégorie : <?=$category?> - <?=$category_name?></p>
                            <p><?=$effect_text?></p>
                            <p><?=$description?></p>   
                            <p><?=$creation_text?></p>               
                          </div>
                        <?php $return['value'] .= ob_get_clean();
                      } 
                  
                  }
              }
              
              $return['super_category_list'] = $_SESSION['super_category_text'];
              $return['category_list'] = $_SESSION['category_text'];

              $return['token'] = $this->generateAndSaveToken();
              $return["state"] = true;
            }

          } else {
            $return["value"] = "L'URL n'est pas valide ou la limite est incorrecte";
          }

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

  public function cleanImage(){
    $return = [
      'state' => false,
      'value' => "<p>"
    ];

    $headers = getallheaders();
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    if(isset($token)) {
      if($this->isTokenValid($token)) {

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
        foreach($list_manager as $manager) {
          $obj = $manager["manager"]->getAll();
          $path_obj = [];
          foreach ($obj as $item) {
            $path_obj[] = $item->getFile($manager['name'], new Style(["display" => Content::FORMAT_BRUT]));
          }
          $files = glob($manager["path"] . "*");
          foreach ($files as $file) {
            if (is_file($file)) {
              if(!in_array($file, $path_obj)){
                $path_trash = self::TRASH_PATH ."/" . $manager['trash_name'] . '/' . basename($file);
                FileManager::move($file, $path_trash);
                $n++;
                $return["value"] .= "Le fichier " . $file . " a été déplacé dans la corbeille<br><img width='100px' src='".$path_trash."'><br><br>";
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

}
