<?php ob_start();

// instruct browser to download the PDF
    header("Content-Type: application/x-pdf");
    header("Content-Disposition: attachment; filename=jdrDofus-". date("Y-m-d-H-i") . ".pdf");
    header("Cache-Control: no-cache, must-revalidate");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    </head>

    <body>

        <header>
            <p class="center">
                <?php $img = $_SERVER["DOCUMENT_ROOT"]."/medias/Logos/logo.png"?>
                <img src="<?=$img?>" height="20">
            </p>
        </header>
        <footer>
            <table width="100%">
                <tr width="100%">
                    <td align="left" width="30%" class="size-0-7 text-grey">Fiche générée le <?=date("d/m/Y")?> à <?=date("H:i")?></td>
                    <td align="center" width="30%"><span class="pagenum"></span></td>
                </tr>
            </table>
        </footer>

<?php $content = ob_get_clean();