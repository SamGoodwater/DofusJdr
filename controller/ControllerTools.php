<?php
class ControllerTools extends Controller{
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
}
