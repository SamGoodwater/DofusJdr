<?php

declare(strict_types=1);

/**
 * États jouables ajoutés au catalogue (D&D 2014 + états Dofus retenus).
 *
 * Les fiches déjà `playable` du même nom ne sont pas réécrites.
 * `aliases` sert seulement à reconnaître un jeton Brut déjà en base.
 *
 * @return list<array{name: string, description: string, image: string|null, aliases?: list<string>, flags?: list<string>}>
 */
$img = static fn (string $file): string => 'https://api.dofusdb.fr/img/states/'.$file;
$local = static fn (string $file): string => '/storage/images/icons/states/'.$file;

return [
    [
        'name' => 'À terre',
        'description' => "Une créature à terre ne peut que ramper. Se relever met fin à l’état.\nLa créature a un désavantage aux jets d’attaque.\nUn jet d’attaque contre elle a un avantage si l’attaquant est à 1 case ou moins. Sinon, ce jet a un désavantage.",
        'image' => $local('a-terre.png'),
    ],
    [
        'name' => 'Agrippé',
        'description' => "Les PM d’une créature agrippée passent à 0, quels que soient ses bonus.\nL’état prend fin si la créature qui agrippe est incapable d’agir.\nL’état prend aussi fin si un effet éloigne l’agrippé hors de portée de ce qui l’agrippe.",
        'image' => $local('agrippe.png'),
    ],
    [
        'name' => 'Assourdi',
        'description' => 'Une créature assourdie n’entend pas et rate automatiquement tout jet de caractéristique qui nécessite l’ouïe.',
        'image' => $local('assourdi.png'),
    ],
    [
        'name' => 'Charmé',
        'description' => "Une créature charmée ne peut pas attaquer le charmeur, ni le cibler avec un effet nuisible.\nLe charmeur a un avantage aux jets de caractéristique pour interagir socialement avec elle.",
        'image' => $local('charme.png'),
    ],
    [
        'name' => 'Effrayé',
        'description' => "Une créature effrayée a un désavantage aux jets de caractéristique et aux jets d’attaque tant que la source de sa peur est dans sa ligne de vue.\nElle ne peut pas s’en rapprocher d’elle-même.",
        'image' => $local('effraye.png'),
    ],
    [
        'name' => 'Entravé',
        'description' => "Les PM d’une créature entravée passent à 0, quels que soient ses bonus.\nLes jets d’attaque contre elle ont un avantage, et ses jets d’attaque ont un désavantage.\nElle a un désavantage à ses jets de sauvegarde d’Agilité.",
        'image' => $local('entrave.png'),
    ],
    [
        'name' => 'Épuisement',
        'description' => "L’épuisement se compte en six niveaux. La créature subit le niveau actuel et tous ceux d’en dessous.\n1 — désavantage aux jets de caractéristique.\n2 — PM réduits de moitié.\n3 — désavantage aux jets d’attaque et de sauvegarde.\n4 — points de vie maximum réduits de moitié.\n5 — PM à 0.\n6 — mort.\nUn repos long, à condition d’avoir mangé et bu, retire 1 niveau. Revenir à la vie retire aussi 1 niveau.",
        'image' => $local('epuisement.png'),
    ],
    [
        'name' => 'Incapable d\'agir',
        'description' => 'Une créature incapable d’agir ne fait plus ni action ni réaction : ses PA passent à 0, quels que soient ses bonus.',
        'image' => $local('incapable-d-agir.png'),
        'flags' => ['prevents_spell_cast'],
    ],
    [
        'name' => 'Inconscient',
        'description' => "Une créature inconsciente est incapable d’agir (PA = 0, quels que soient ses bonus), ne peut plus bouger (PM = 0) ni parler, et ne perçoit plus ce qui l’entoure.\nElle lâche ce qu’elle tenait et tombe à terre.\nElle rate automatiquement ses jets de sauvegarde de Force et d’Agilité.\nLes jets d’attaque contre elle ont un avantage.\nToute attaque qui la touche est un coup critique si l’attaquant est à 1 case ou moins.",
        'image' => $local('inconscient.png'),
        'flags' => ['prevents_spell_cast'],
    ],
    [
        'name' => 'Invisible',
        'description' => "Une créature invisible ne peut pas être vue sans magie ou un sens particulier. Pour se cacher, elle compte comme étant dans une zone sans visibilité. Un bruit ou une trace peut révéler sa case.\nLes jets d’attaque contre elle ont un désavantage, et ses jets d’attaque ont un avantage.",
        'image' => $local('invisible.png'),
    ],
    [
        'name' => 'Paralysé',
        'description' => "Une créature paralysée est incapable d’agir (PA = 0, quels que soient ses bonus) et ne peut plus bouger (PM = 0) ni parler.\nElle rate automatiquement ses jets de sauvegarde de Force et d’Agilité.\nLes jets d’attaque contre elle ont un avantage.\nToute attaque qui la touche est un coup critique si l’attaquant est à 1 case ou moins.",
        'image' => $local('paralyse.png'),
        'flags' => ['prevents_spell_cast'],
    ],
    [
        'name' => 'Pétrifié',
        'description' => "Une créature pétrifiée, ainsi que les objets non magiques qu’elle porte, devient une matière inerte (souvent la pierre). Son poids est multiplié par dix et elle ne vieillit plus.\nElle est incapable d’agir (PA = 0), ne peut plus bouger (PM = 0) ni parler, et ne perçoit plus ce qui l’entoure.\nLes jets d’attaque contre elle ont un avantage.\nElle rate automatiquement ses jets de sauvegarde de Force et d’Agilité.\nElle a la résistance à tous les dégâts.\nLe poison et la maladie ne l’affectent plus. Un poison ou une maladie déjà présent est suspendu, pas annulé.",
        'image' => $local('petrifie.png'),
        'flags' => ['prevents_spell_cast'],
    ],
    [
        'name' => 'Altruiste',
        'description' => 'Les soins destinés aux alliés de la créature lui sont redirigés. Les alliés ne les reçoivent pas.',
        'image' => $local('altruiste.png'),
    ],
    [
        'name' => 'Enraciné',
        'description' => 'La créature ne peut pas être déplacée : ni attirée, ni repoussée, ni échangée de place. Ses PM restent utilisables.',
        'image' => $img('stateRooted.png'),
        'flags' => ['cant_be_moved', 'cant_be_pushed', 'cant_switch_position'],
    ],
    [
        'name' => 'Indéplaçable',
        'description' => 'Aucun effet ne change la position de la créature : ni attraction, ni poussée, ni échange de place.',
        'image' => $img('stateUnshift.png'),
        'flags' => ['cant_be_moved', 'cant_be_pushed', 'cant_switch_position'],
    ],
    [
        'name' => 'Insoignable',
        'description' => 'La créature ne peut pas être soignée.',
        'image' => $img('stateInsoignable.png'),
        'flags' => ['incurable'],
    ],
    [
        'name' => 'Invulnérable',
        'description' => 'La créature ne subit aucun dégât.',
        'image' => $img('stateInvulnerable.png'),
        'flags' => ['invulnerable'],
    ],
    [
        'name' => 'Invulnérable à distance',
        'description' => 'La créature ne subit aucun dégât des attaques et des sorts à distance.',
        'image' => $img('stateInvulnerableDistance.png'),
        'flags' => ['invulnerable_range'],
    ],
    [
        'name' => 'Invulnérable au càc',
        'description' => 'La créature ne subit aucun dégât des attaques et des sorts au corps à corps.',
        'image' => $img('stateInvulnerableMelee.png'),
        'aliases' => ['Invulnérable en Mêlée'],
        'flags' => ['invulnerable_melee'],
    ],
    [
        'name' => 'Invulnérable à l\'élément Terre',
        'description' => 'La créature ne subit aucun dégât de Terre.',
        'image' => $img('stateEarth.png'),
    ],
    [
        'name' => 'Invulnérable à l\'élément Feu',
        'description' => 'La créature ne subit aucun dégât de Feu.',
        'image' => $img('stateFire.png'),
    ],
    [
        'name' => 'Invulnérable à l\'élément Air',
        'description' => 'La créature ne subit aucun dégât d’Air.',
        'image' => $img('stateAir.png'),
    ],
    [
        'name' => 'Invulnérable à l\'élément Eau',
        'description' => 'La créature ne subit aucun dégât d’Eau.',
        'image' => $img('stateWater.png'),
    ],
    [
        'name' => 'Lourd',
        'description' => 'La créature ne peut pas être déplacée : ni attirée, ni repoussée.',
        'image' => $img('stateImmovable.png'),
        'flags' => ['cant_be_moved', 'cant_be_pushed'],
    ],
    [
        'name' => 'Pacifiste',
        'description' => 'La créature ne peut pas infliger de dégâts.',
        'image' => $img('statePacifist.png'),
        'flags' => ['cant_deal_damage'],
    ],
    [
        'name' => 'Pacifiste à distance',
        'description' => 'La créature ne peut pas infliger de dégâts à distance.',
        'image' => $local('pacifiste-distance.png'),
    ],
    [
        'name' => 'Pacifiste au càc',
        'description' => 'La créature ne peut pas infliger de dégâts au corps à corps.',
        'image' => $local('pacifiste-cac.png'),
    ],
    [
        'name' => 'Saoul',
        'description' => 'La créature a un désavantage à ses jets de sauvegarde, à ses jets de compétence et à ses jets d’attaque à distance.',
        'image' => $local('saoul.png'),
    ],
    [
        'name' => 'Intaclable',
        'description' => 'La créature ne peut pas être taclée. Elle réussit automatiquement ses jets de fuite.',
        'image' => $img('stateIntaclable.png'),
        'aliases' => ['Intacable'],
        'flags' => ['cant_be_tackled'],
    ],
    [
        'name' => 'Intacleur',
        'description' => 'La créature ne peut pas tacler.',
        'image' => $img('stateIntacleur.png'),
        'flags' => ['cant_tackle'],
    ],
    [
        'name' => 'Terrorisé',
        'description' => "Une créature terrorisée a un désavantage à ses jets de caractéristique et à ses jets d’attaque tant que la source de sa frayeur est dans sa ligne de vue.\nElle ne peut pas s’en rapprocher de son plein gré.",
        'image' => $local('terrorise.png'),
    ],
    [
        'name' => 'Résistant',
        'description' => 'Les dégâts subis par la créature sont divisés par 2.',
        'image' => $local('resistant.png'),
    ],
    [
        'name' => 'Résistant à distance',
        'description' => 'Les dégâts à distance subis par la créature sont divisés par 2.',
        'image' => $local('resistant-distance.png'),
    ],
    [
        'name' => 'Résistant au càc',
        'description' => 'Les dégâts au corps à corps subis par la créature sont divisés par 2.',
        'image' => $local('resistant-cac.png'),
    ],
    [
        'name' => 'Résistant à l\'élément Terre',
        'description' => 'Les dégâts de Terre subis par la créature sont divisés par 2.',
        'image' => $local('resistant-terre.png'),
    ],
    [
        'name' => 'Résistant à l\'élément Feu',
        'description' => 'Les dégâts de Feu subis par la créature sont divisés par 2.',
        'image' => $local('resistant-feu.png'),
    ],
    [
        'name' => 'Résistant à l\'élément Air',
        'description' => 'Les dégâts d’Air subis par la créature sont divisés par 2.',
        'image' => $local('resistant-air.png'),
    ],
    [
        'name' => 'Résistant à l\'élément Eau',
        'description' => 'Les dégâts d’Eau subis par la créature sont divisés par 2.',
        'image' => $local('resistant-eau.png'),
    ],
];
