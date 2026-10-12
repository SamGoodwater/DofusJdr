<?php

declare(strict_types=1);

/**
 * Mode d’application des seeders.
 *
 * Par défaut (`overwrite` = false), les seeders ne créent que les lignes absentes :
 * la base est la source de vérité. Les fichiers sous database/seeders/data/ sont
 * une copie versionnée, réécrite hors production lors de project:backup.
 *
 * DANGER — SEED_OVERWRITE=true ou `project:seed --overwrite` réécrit les lignes
 * existantes (updateOrCreate). Les commandes projet demandent une confirmation
 * interactive ; en CI : `project:seed --overwrite -y`. Toujours exporter la base
 * vers les JSON avant un overwrite (`scrapping:seeders:export --characteristics`).
 */
return [

    'overwrite' => (bool) env('SEED_OVERWRITE', false),

];
