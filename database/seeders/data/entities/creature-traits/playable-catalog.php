<?php

declare(strict_types=1);

/**
 * Traits permanents jouables.
 *
 * Les neuf fiches historiques (Malade, Lourd, tailles, poisons, Vif / Vive, Agile) gardent leur texte.
 * Les autres viennent des traits optionnels de D&D 3.5, réécrits avec les compétences,
 * les sauvegardes et l’avantage / le désavantage de KrosmozJDR.
 * École prédestinée n’est pas repris : il n’y a ni écoles de magie ni niveau de lanceur.
 * Le trait de vitesse s’appelle Rapide : Vif / Vive est déjà l’initiative.
 *
 * @return list<array{name: string, description: string, image: string}>
 */
$icon = static fn (string $file): string => '/storage/images/icons/traits/'.$file;

return [
    [
        'name' => 'Malade',
        'description' => 'Désavantage sur les tests de Force et d’Agilité selon les règles.',
        'image' => $icon('malade.png'),
    ],
    [
        'name' => 'Lourd',
        'description' => 'Difficile à déplacer et incapable de se déplacer sur des sols fragiles.',
        'image' => $icon('lourd.png'),
    ],
    [
        'name' => 'Petite taille',
        'description' => 'Avantage en discrétion, mais déplacement réduit.',
        'image' => $icon('petite-taille.png'),
    ],
    [
        'name' => 'Grande taille',
        'description' => 'Avantage en intimidation, désavantage en discrétion.',
        'image' => $icon('grande-taille.png'),
    ],
    [
        'name' => 'Gigantesque',
        'description' => 'Grande taille extrême avec avantages de Force et perception.',
        'image' => $icon('gigantesque.png'),
    ],
    [
        'name' => 'Insensible aux poisons',
        'description' => 'Ne peut pas être empoisonné.',
        'image' => $icon('insensible-aux-poisons.png'),
    ],
    [
        'name' => 'Métaboliseur rapide',
        'description' => 'Ne peut pas être empoisonné par ingestion.',
        'image' => $icon('metaboliseur-rapide.png'),
    ],
    [
        'name' => 'Vif / Vive',
        'description' => 'Commence le combat en premier sans jet d’initiative.',
        'image' => $icon('vif.png'),
    ],
    [
        'name' => 'Agile',
        'description' => 'Réussit automatiquement ses jets de fuite et ne peut pas être taclé.',
        'image' => $icon('agile.png'),
    ],
    [
        'name' => 'Abrupt',
        'description' => 'Avantage aux jets d’Intimidation. Désavantage aux jets de Supercherie et de Persuasion.',
        'image' => $icon('abrupt.png'),
    ],
    [
        'name' => 'Absorbé',
        'description' => 'Avantage aux jets d’Arcanes, d’Histoire, de Nature et de Religion. Désavantage aux jets de Perception.',
        'image' => $icon('absorbe.png'),
    ],
    [
        'name' => 'Agressif',
        'description' => 'Avantage au jet d’initiative. La CA diminue de 1.',
        'image' => $icon('agressif.png'),
    ],
    [
        'name' => 'Amorphe',
        'description' => 'Avantage aux jets de sauvegarde de Sagesse contre le charme. Désavantage au jet d’initiative.',
        'image' => $icon('amorphe.png'),
    ],
    [
        'name' => 'Appliqué',
        'description' => 'Avantage aux jets pour maintenir sa concentration. Désavantage aux jets de Perception.',
        'image' => $icon('applique.png'),
    ],
    [
        'name' => 'Bagarreur',
        'description' => 'Avantage aux attaques à mains nues et pour agripper. Désavantage aux autres jets d’attaque. Les armes naturelles n’en profitent pas.',
        'image' => $icon('bagarreur.png'),
    ],
    [
        'name' => 'Brutal',
        'description' => 'Désavantage aux jets d’attaque au corps à corps. Avantage aux jets de dégâts de ces attaques.',
        'image' => $icon('brutal.png'),
    ],
    [
        'name' => 'Cavalier-né',
        'description' => 'Avantage aux jets de Dressage pour monter ou diriger une monture. Désavantage aux autres jets de Dressage.',
        'image' => $icon('cavalier-ne.png'),
    ],
    [
        'name' => 'Corpulent',
        'description' => 'Avantage aux jets de Force pour ne pas être poussé ou renversé. Désavantage aux jets d’Acrobaties pour se dégager.',
        'image' => $icon('corpulent.png'),
    ],
    [
        'name' => 'Courageux',
        'description' => 'Avantage aux jets de sauvegarde de Sagesse. Désavantage aux jets de sauvegarde de Vitalité.',
        'image' => $icon('courageux.png'),
    ],
    [
        'name' => 'Désinvolte',
        'description' => 'Avantage aux jets de sauvegarde de Sagesse. Désavantage aux jets de sauvegarde d’Agilité.',
        'image' => $icon('desinvolte.png'),
    ],
    [
        'name' => 'Dur d\'oreille',
        'description' => 'Avantage aux jets de Perception qui reposent sur la vue. Désavantage à ceux qui reposent sur l’ouïe.',
        'image' => $icon('dur-d-oreille.png'),
    ],
    [
        'name' => 'Engageant',
        'description' => 'Avantage aux jets de Persuasion pour obtenir des renseignements. Désavantage aux jets d’Intimidation et de Perspicacité.',
        'image' => $icon('engageant.png'),
    ],
    [
        'name' => 'Honnête',
        'description' => 'Avantage aux jets de Persuasion. Désavantage aux jets de Supercherie et de Perspicacité.',
        'image' => $icon('honnete.png'),
    ],
    [
        'name' => 'Hypermétrope',
        'description' => 'Avantage aux jets de Perception à distance. Désavantage aux jets d’Investigation de près.',
        'image' => $icon('hypermetrope.png'),
    ],
    [
        'name' => 'Infatigable',
        'description' => 'Avantage aux jets de Vitalité pour prolonger un effort. Ce qui ne devrait que fatiguer impose l’Épuisement.',
        'image' => $icon('infatigable.png'),
    ],
    [
        'name' => 'Insaisissable',
        'description' => 'Avantage pour échapper à une prise. Désavantage aux autres jets de lutte.',
        'image' => $icon('insaisissable.png'),
    ],
    [
        'name' => 'Lent',
        'description' => 'Gagne 1 PV de plus à chaque niveau. Ses PM de base sont divisés par 2.',
        'image' => $icon('lent.png'),
    ],
    [
        'name' => 'Maigre',
        'description' => 'Avantage aux jets d’Acrobaties pour se faufiler. Désavantage aux jets de Force contre une poussée ou un renversement.',
        'image' => $icon('maigre.png'),
    ],
    [
        'name' => 'Malhonnête',
        'description' => 'Avantage aux jets de Supercherie. Désavantage aux jets de Persuasion.',
        'image' => $icon('malhonnete.png'),
    ],
    [
        'name' => 'Méfiant',
        'description' => 'Avantage aux jets de Perspicacité. Désavantage aux jets de Persuasion et d’Intimidation.',
        'image' => $icon('mefiant.png'),
    ],
    [
        'name' => 'Musculeux',
        'description' => 'Avantage aux jets de Force et aux compétences de Force. Désavantage aux jets d’Agilité et aux compétences d’Agilité.',
        'image' => $icon('musculeux.png'),
    ],
    [
        'name' => 'Myope',
        'description' => 'Avantage aux jets d’Investigation de près. Désavantage aux jets de Perception à distance.',
        'image' => $icon('myope.png'),
    ],
    [
        'name' => 'Nyctalope',
        'description' => 'Voit 2 cases plus loin dans le noir. Désavantage aux jets de Perception en pleine lumière. Il faut déjà voir dans le noir.',
        'image' => $icon('nyctalope.png'),
    ],
    [
        'name' => 'Passionné',
        'description' => 'Avantage aux jets de sauvegarde de Vitalité. Désavantage aux jets de sauvegarde de Sagesse.',
        'image' => $icon('passionne.png'),
    ],
    [
        'name' => 'Poli',
        'description' => 'Avantage aux jets de Persuasion. Désavantage aux jets d’Intimidation.',
        'image' => $icon('poli.png'),
    ],
    [
        'name' => 'Prudent',
        'description' => 'Sans effet si la créature est immunisée à la terreur. Sinon, +1 CA quand elle n’attaque pas dans son tour, et désavantage aux sauvegardes contre la terreur.',
        'image' => $icon('prudent.png'),
    ],
    [
        'name' => 'Sauvage',
        'description' => 'Avantage aux jets de Dressage. Désavantage aux jets de Supercherie et de Persuasion.',
        'image' => $icon('sauvage.png'),
    ],
    [
        'name' => 'Spécialisé',
        'description' => 'Choisit Arcanes, Histoire, Nature ou Religion : avantage à celle-ci, désavantage aux trois autres.',
        'image' => $icon('specialise.png'),
    ],
    [
        'name' => 'Rapide',
        'description' => 'Ses PM de base augmentent de 2. Enlève 1 PV gagné à chaque niveau, y compris au niveau 1 (0 possible).',
        'image' => $icon('rapide.png'),
    ],
    [
        'name' => 'Vigoureux',
        'description' => 'Avantage aux jets de sauvegarde de Vitalité. Désavantage aux jets de sauvegarde d’Agilité.',
        'image' => $icon('vigoureux.png'),
    ],
];
