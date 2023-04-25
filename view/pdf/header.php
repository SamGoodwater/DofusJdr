<?php ob_start();

// instruct browser to download the PDF
    header("Content-Type: application/x-pdf");
    header("Content-Disposition: attachment; filename=jdrDofus-". date("Y-m-d-H-i") . ".pdf");
    header("Cache-Control: no-cache, must-revalidate");

    $icons = array();
    foreach (scandir("medias/icons") AS $file){
        if(!is_dir($file) && $file != '.' && $file != '..'){
            $img = New File("medias/icons/".$file);
            if(FileManager::isImage($img)){
                $icons[$img->getName(Content::FORMAT_BRUT, false)] = "data:image/".$img->getExtention().";base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $img->getPath()));
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <style type="text/css">
            * {
                font-family: Roboto !important;
            }
            header,
			footer {
			    width: 100%;
			    text-align: center;
			    position: fixed;
			}
			header {
			    top: 0px;

			}
			footer {
			    bottom: 0px;
			}
			.pagenum:before {
			    content: counter(page);
			}
            html, body {
                margin: 20px 5px;
                min-height: 100%;
                padding: 5px;
                font-size: 12px;
                line-height: 13px;
                word-wrap: break-word;
                letter-spacing: normal;
            }   
            h3, p, div, span, tr, td, table{
                margin: 0px;
                padding: 0px;
            }
            .p1{padding: 3px;}.pt1{padding-top: 3px;}
            .p2{padding: 6px;}.pt2{padding-top: 6px;}
            .p3{padding: 12px;}.pt3{padding-top: 12px;}
            .p4{padding: 20px;}.pt4{padding-top: 20px;}
            .p5{padding: 30px;}.pt5{padding-top: 30px;}
            .m1{margin: 3px;}.mt1{margin-top: 3px;}.mr1{margin-right: 3px;}
            .m2{margin: 6px;}.mt2{margin-top: 6px;}.mr2{margin-right: 6px;}
            .m3{margin: 12px;}.mt3{margin-top: 12px;}.mr3{margin-right: 12px;}
            .m4{margin: 20px;}.mt4{margin-top: 20px;}.mr4{margin-right: 20px;}
            .m5{margin: 30px;}.mt5{margin-top: 30px;}.mr5{margin-right: 30px;}
            .center{text-align: center;}   
            .left{text-align: left;}   
            .right{text-align: right;}
            .baseline{vertical-align: baseline;}
            .middle{vertical-align: middle;}
            .starter{
                color:#616161;
                font-size: 10px;
            }
            .box-calcule{
                color:<?=Style::COLOR_TO_HEX['grey-d-3']?>;
                font-size: 8px;
                padding: 1px;
                margin: 0px;
                text-align: center;
            }
            .enavant{
                font-size: 17px;
                font-weight: bold;
            }
            .enavant2{
                font-size: 13px;
                font-weight: bold;
            }

            td, tr{
                border-radius: 5px;
            }
            .box-level{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['level'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['level'].'-d-3']?>;
                border-radius: 4px;
            }
            .box-level .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['level'].'-d-3']?>;
            }
            .box-pa{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['pa'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['pa'].'-d-3']?>;
                border-radius: 4px;
            }
            .box-pa .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['pa'].'-d-3']?>;
            }
            .box-pm{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['pm'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['pm'].'-d-3']?>;
                border-radius: 4px;
            }
            .box-pm .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['pm'].'-d-3']?>;
            }
            .box-po{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['po'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['po'].'-d-3']?>;
                border-radius: 4px;
            }
            .box-po .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['po'].'-d-3']?>;
            }
            .box-ini{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['ini'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['ini'].'-d-3']?>;
                border-radius: 4px;
            }
            .box-ini .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['ini'].'-d-3']?>;
            }
            .box-touch{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['touch'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['touch'].'-d-3']?>;
                border-radius: 4px;
            }
            .box-touch .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['touch'].'-d-3']?>;
            }
            .box-life{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['life'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['life'].'-d-3']?>;
                border-radius: 4px;
            }
            .box-life .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['life'].'-d-3']?>;
            }
            .box-dodge_pm{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['dodge_pm'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['dodge_pm'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-dodge_pm .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['dodge_pm'].'-d-3']?>;
            }
            .box-dodge_pa{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['dodge_pa'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['dodge_pa'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-dodge_pa .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['dodge_pa'].'-d-3']?>;
            }
            .box-ca{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['ca'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['ca'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-ca .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['ca'].'-d-3']?>;
            }
            .box-fuite{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['fuite'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['fuite'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-fuite .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['fuite'].'-d-3']?>;
            }
            .box-tacle{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['tacle'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['tacle'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-tacle .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['tacle'].'-d-3']?>;
            }
            .box-strong{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['strong'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['strong'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-strong .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['strong'].'-d-3']?>;
            }
            .box-intel{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['intel'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['intel'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-intel .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['intel'].'-d-3']?>;
            }
            .box-chance{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['chance'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['chance'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-chance .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['chance'].'-d-3']?>;
            }
            .box-agi{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['agi'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['agi'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-agi .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['agi'].'-d-3']?>;
            }
            .box-shield{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['shield'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['shield'].'-d-2']?>;
                border-radius: 4px;
            }
            .box-shield .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['shield'].'-d-3']?>;
            }

            .box-neutre{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['neutre'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['neutre'].'-d-2']?>;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-neutre .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['neutre'].'-d-3']?>;
            }
            .box-vitality{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['vitality'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['vitality'].'-d-2']?>;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-vitality .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['vitality'].'-d-3']?>;
            }
            .box-sagesse{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['sagesse'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['sagesse'].'-d-2']?>;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-sagesse .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['sagesse'].'-d-3']?>;
            }
            .box-terre{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['terre'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['terre'].'-d-2']?>;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-terre .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['terre'].'-d-3']?>;
            }
            .box-feu{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['feu'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['feu'].'-d-2']?>;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-feu .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['feu'].'-d-3']?>;
            }
            .box-eau{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['eau'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['eau'].'-d-2']?>;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-eau .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['eau'].'-d-3']?>;
            }
            .box-air{
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['air'].'-l-5']?>;
                border: 2px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['air'].'-d-2']?>;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-air .enavant2{
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['air'].'-d-3']?>;
            }
            .box-terre-feu{
                background-color: #fff3e0;
                border: 2px solid #e65100;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-terre-eau{
                background-color: #e0f7fa;
                border: 2px solid #006064;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-terre-air{
                background-color: #e0f2f1;
                border: 2px solid #004d40;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-feu-eau{
                background-color: #fce4ec;
                border: 2px solid #880e4f;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-feu-air{
                background-color: #f9fbe7;
                border: 2px solid #827717;
                border-radius: 4px;
                padding: 2px 4px;
            }
            .box-air-eau{
                background-color: #eceff1;
                border: 2px solid #263238;
                border-radius: 4px;
                padding: 2px 4px;
            }

            .size-0-6{font-size: 0.6em;}
            .size-0-7{font-size: 0.7em;}
            .size-0-8{font-size: 0.8em;}
            .size-0-9{font-size: 0.9em;}
            .size-1{font-size: 1em;}
            .size-1-2{font-size: 1.2em;}
            .size-1-3{font-size: 1.3em;}
            .size-1-4{font-size: 1.4em;}
            .size-1-5{font-size: 1.5em;}
            .size-1-8{font-size: 1.8em;}
            .size-2{font-size: 2em;}
            .size-2-5{font-size: 2.5em;}
            .size-3{font-size: 3em;}
            .size-4{font-size: 4em;}
            .size-5{font-size: 5em;}
            .size-6{font-size: 6em;}
            .size-7{font-size: 7em;}

            .border{
                border-width: 1px;
                border-style: solid;
            }
            .badge{
                border-radius: 3px;
                padding: 2px 4px;
                display: inline-block;
                font-weight: 700;
                line-height: 1;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
            }
            .magic{
                background-color: <?=Style::COLOR_TO_HEX['purple-d-2']?>;   
                color:white;
                font-size: 0.8em;
                margin-top: 5px;
                font-weight: 800;
                border: 1px solid <?=Style::COLOR_TO_HEX['purple-d-2']?>; 
            }
            .physic{
                background-color: <?=Style::COLOR_TO_HEX['brown-d-2']?>;
                color:white;
                font-size: 0.8em;
                margin-top: 5px;
                font-weight: 800;
                border: 1px solid <?=Style::COLOR_TO_HEX['brown-d-2']?>; 
            }
            .powerfull{
                background-color: <?=Style::COLOR_TO_HEX['deep-purple-d-3']?>;
                color:white;
                font-size: 0.8em;
                margin-top: 5px;
                font-weight: 800;
                border: 1px solid <?=Style::COLOR_TO_HEX['deep-purple-d-3']?>; 
            }

            .po{
                font-size: 1em;
                font-weight: 800;
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['po'].'-d-2']?>;
            }
            .pa{
                font-size: 1.3em;
                font-weight: 800;
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['pa'].'-d-5']?>;
            }
            .time_before_use_again{
                font-size: 1.3em;
                font-weight: 800;
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['time_before_use_again'].'-d-5']?>;
            }
            .level{
                font-size: 1em;
                font-weight: 800;
                color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['level'].'-d-2']?>;
                border: 1px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['level'].'-d-2']?>;
                background-color: white;
            }
            .frequency{
                font-size: 1em;
                font-weight: 600;
                color: white;
                border: 1px solid <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['po'].'-d-2']?>;
                background-color: <?=Style::COLOR_TO_HEX[Style::COLOR_CUSTOM['po'].'-d-2']?>;
            }
          
            .colored {
                background-color: #e0e0e0; 
            }
            .text-grey{color:#424242;}

            .relative{position: relative;}
            .absolute{position: absolute;}
        </style>
    </head>

    <body>

    <header>
        <p class="center">
            <?php 
                $file_img = New File($GLOBALS['project']['logo']);
                $img = "data:image/".$file_img->getExtention().";base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $file_img->getPath()));
            ?>
            <img src="<?=$img?>" height="20">
        </p>
    </header>
    <footer>
        <table width="100%">
            <tr width="100%">
                <td align="left" width="30%" class="size-0-7 text-grey">Fiche générée le <?=date("d/m/Y")?> à <?=date("H:i")?></td>
                <td align="center" width="30%" class="size-0-7 text-grey"><?=$GLOBALS['project']['name']?> version <?=$GLOBALS['project']['version']?> <?=$GLOBALS['project']['stability_verbal']?> | <?php echo date("Y");?></td>
                <td align="center" width="30%"><span class="pagenum"></span></td>
            </tr>
        </table>
    </footer>

<?php $content = ob_get_clean();