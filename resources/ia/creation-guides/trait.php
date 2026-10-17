<?php

declare(strict_types=1);

/**
 * Guide de création / conversion : traits de créature.
 *
 * @param  array<string, string>  $k
 * @return array<string, mixed>
 */
return static function (array $k): array {
    return [
        'entity' => 'trait',
        'title' => 'Traits',
        'slug' => 'creation-traits',
        'icon' => 'fa-solid fa-fingerprint',
        'menu_order' => 11,
        'ia_for_conversion' => true,
        'sections' => [
            [
                'id' => 'philosophie',
                'title' => 'Philosophie',
                'html' => '<h2>Traits</h2>'
                    .'<p>Un trait est <strong>permanent</strong> (Lourd, petite taille, vol…) : identité de race, de classe ou de monstre, pas un buff de combat. Les états, eux, sont temporaires. '.$k['kTraitsLivre'].'.</p>'
                    .'<p>Le catalogue JDR est <strong>court à dessein</strong> : on réutilise une fiche existante. Conversion : on ne crée pas un trait par monstre Dofus (« Piou Vert » n’est pas un trait). On coche 0 à 2 traits déjà jouables.</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li><strong>Une phrase d’identité</strong> (« ne peut pas être déplacé », « voit dans le noir »).</li>'
                    .'<li>Si ça donne une stat, reste ligne neutre créature, et ce n’est pas un duplicata d’équipement du même niveau.</li>'
                    .'<li><strong>Un monstre : 0–2 traits lisibles.</strong> Une classe : le passif suffit souvent ; le trait ne le double pas.</li>'
                    .'<li>Avant d’en inventer un : un trait déjà jouable suffit-il ? Pour un monstre, privilégier les neuf tags (taille, Vif, Agile, Lourd, poisons). Les traits de caractère (Abrupt, Courageux, Rapide…) sont plutôt pour un personnage.</li>'
                    .'<li>Ce n’est pas un état (Empoisonné, Étourdi…) — voir [[kref:page:creation-etats|États]].</li>'
                    .'</ol>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites et propriétés',
                'html' => '<h3>Propriétés de fiche</h3>'
                    .'<p>Un trait n’a presque pas de chiffres : <code>name</code>, <code>description</code>, image, état, droits. Pas de '.$k['pa'].', pas de dés, pas de niveau. La puissance se lit dans la phrase (avantage / désavantage / immunité).</p>'
                    .'<h3>Catalogue actuel (à réutiliser)</h3>'
                    .'<ul>'
                    .'<li><strong>Petite taille</strong> — avantage Discrétion, déplacement réduit.</li>'
                    .'<li><strong>Grande taille</strong> — avantage Intimidation, désavantage Discrétion.</li>'
                    .'<li><strong>Gigantesque</strong> — taille extrême, Force et perception.</li>'
                    .'<li><strong>Vif / Vive</strong> — commence le combat en premier, sans jet d’initiative.</li>'
                    .'<li><strong>Agile</strong> — fuite auto, ne peut pas être taclé.</li>'
                    .'<li><strong>Lourd</strong> — difficile à déplacer, sols fragiles interdits.</li>'
                    .'<li><strong>Malade</strong> — désavantage Force et Agilité.</li>'
                    .'<li><strong>Insensible aux poisons</strong> / <strong>Métaboliseur rapide</strong> — immunités ciblées.</li>'
                    .'</ul>'
                    .'<p>S’y ajoutent des traits de caractère permanents (avantage et désavantage, jamais un état temporaire) : Abrupt, Absorbé, Agressif, Amorphe, Appliqué, Bagarreur, Brutal, Cavalier-né, Corpulent, Courageux, Désinvolte, Dur d’oreille, Engageant, Honnête, Hypermétrope, Infatigable, Insaisissable, Lent, Maigre, Malhonnête, Méfiant, Musculeux, Myope, Nyctalope, Passionné, Poli, Prudent, Sauvage, Spécialisé, Rapide, Vigoureux. Rapide est la vitesse ; Vif / Vive reste l’initiative.</p>'
                    .'<p>Pas de charte niveau × puissance : le trait ne scale pas. Un Piou 2 et un boss 18 « petite taille » ont le même trait.</p>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<h3>Conseils</h3>'
                    .'<ul>'
                    .'<li>Pose le trait seulement s’il change une décision à la table (se cacher, tacle, initiative).</li>'
                    .'<li>Un sbire n’a pas besoin de cinq traits de combat.</li>'
                    .'<li>Si tu écris « +2 Force permanente », c’est une carac ou un équipement, pas un trait.</li>'
                    .'</ul>'
                    .'<p><strong>À éviter :</strong> trait = cape +2 ; inventer « Volant du Moskito » alors que Petite taille + Vif suffisent ; publier un trait brut scrapé.</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<h3>Étalons jouables</h3>'
                    .'<ul>'
                    .'<li><strong>Piou Vert</strong> — Petite taille + Vif / Vive.</li>'
                    .'<li><strong>Moskito</strong> — Petite taille + Vif + Agile (le maximum utile : 3, déjà chargé).</li>'
                    .'<li>Invocations de classe : en général aucun trait ; la fragilité est dans les '.$k['pv'].' (8/12/16).</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-creature-trait|Traits]].</p>',
            ],
        ],
        'cms_extras' => [],
    ];
};
