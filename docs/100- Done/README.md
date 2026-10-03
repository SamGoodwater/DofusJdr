# Ce qui a été fait

## Octobre 2026 — L’auteur d’un sort peut enregistrer ses degrés

Un auteur (même simple utilisateur) qui a le droit de modifier sa fiche peut aussi enregistrer les degrés et effets. Avant, l’API exigeait le rôle MJ : l’éditeur s’ouvrait, mais le bouton Enregistrer échouait dès qu’un degré était modifié.

## Octobre 2026 — Enregistrement d’un sort ne perd plus les dégâts chiffrés

Sur les sorts venus de l’ancien modèle, certains effets stockent encore les dégâts en dés ou en min–max plutôt qu’en formule. Un enregistrement (même pour changer seulement le coût PA ou le niveau d’un degré) renvoyait les effets sans ces chiffres : ils disparaissaient de la fiche. L’enregistrement renvoie maintenant ces valeurs telles quelles.

## Octobre 2026 — Droits de lecture après une maj DofusDB

Rétablir la version d’avant une actualisation DofusDB (ou une conversion IA) ne change plus les seuils de lecture et d’écriture d’une fiche, sauf pour un admin. Un MJ qui annule une maj ne rouvre plus accidentellement une fiche restreinte.

## Octobre 2026 — Résistances d’une fiche neuve

Créer un PNJ ou un monstre vide ne force plus les résistances fixes à zéro. Ces champs restent vides : l’équipement et les formules s’ajoutent comme pour les autres caractéristiques. Un zéro écrit à la main reste un total explicite.

## Octobre 2026 — Bonus d’équipement de nouveau dans les totaux runtime

Les formules créature lisent `[strength_object]`, `[initiative_object]`, `[wakfu_recharge_object]`, etc. Après l’unification des formules (termes objet dans la formule, plus d’ajout arithmétique de repli), le runtime injectait encore `strength_creature_object`. Les placeholders manquants valaient 0 : l’équipement apparaissait dans la décomposition mais n’entrait plus dans le total. L’injection reprend les clés courtes d’agrégation.

## Octobre 2026 — Enregistrement des fiches campagne, scénario et boutique

Le bouton **Enregistrer** des pages Modifier (header sheet) écrit maintenant le nom, la description et les autres champs du formulaire. Avant, un scénario ou une boutique validait la requête puis ne persistait rien ; une campagne renvoyait du JSON brut, ce qui cassait le retour Inertia.

## Octobre 2026 — Sorts de classe et édition en modal

Sur la fiche d’une classe, les sorts s’affichent en cartes de même largeur, avec le menu d’options (afficher, modifier) et l’ouverture au double-clic. Le passif de classe se distingue visuellement des sorts. Modifier une entité depuis une carte ou une fiche en modal ouvre l’éditeur dans une modal, sans quitter la page en cours ; depuis la page de l’entité, l’édition reste en page pleine. Après enregistrement en modal, on reste sur la page d’origine (le modal se ferme) pour toutes les entités, y compris les capacités.

## Octobre 2026 — Le Crâ est jouable

La classe Crâ peut se jouer : ses 12 choix de sorts (les quatre voies et les sorts généraux de la fiche) sont sur la classe, ainsi que le passif Flèche guidée par le Wakfu. Une flèche imprégnée contourne un obstacle, une fois entre deux repos longs, puis une fois entre deux repos courts à partir du niveau 10. Chaque sort du kit a une icône, reprise de DofusDB quand le sort existe.

## Octobre 2026 — DD de sauvegarde par élément

Chaque créature a désormais six **DD de sauvegarde** (Force, Intelligence, Agilité, Chance, Vitalité, Sagesse) : le seuil que la cible doit atteindre quand un effet demande une save de cet élément. Formule : 10 + modificateur + bonus d’arme (jusqu’à +5, sans forgemagie) + bonus temporaires de sort ou de consommable. Affichés sous les jets de sauvegarde sur la fiche, et documentés dans les règles de caractéristiques et de lancement de sorts.

## Octobre 2026 — Traits de caractère

Le catalogue des traits permanents reprend les traits optionnels de Donjons & Dragons, réécrits avec les compétences, les sauvegardes et l’avantage ou le désavantage du jeu. Chacun a une icône en losange. École prédestinée n’est pas repris : le jeu n’a pas d’écoles de magie. Le trait de vitesse s’appelle Rapide, pour laisser Vif / Vive à l’initiative. Les neuf traits déjà en place (tailles, Lourd, Agile, poisons…) gardent leur texte.

## Octobre 2026 — États D&D et états Dofus

Le catalogue des états jouables reprend les états de D&D (À terre, Agrippé, Charmé, Épuisement, etc.) et les états Dofus utiles (Altruiste, Enraciné, Invulnérable, Pacifiste, Saoul, et les variantes). Les fiches déjà jouables ne sont pas réécrites. Les états encore en brut, issus du scrap, sont retirés. Saoul donne un désavantage aux sauvegardes, aux compétences et aux attaques à distance. Lourd empêche d’être attiré ou repoussé. Chaque état a une icône : le badge Dofus quand le fichier existe, sinon une pastille dessinée dans le même style. S’y ajoutent Intaclable (fuite automatique, ne peut pas être taclé), Intacleur (ne peut pas tacler), Terrorisé, et Résistant avec ses variantes : les dégâts concernés sont divisés par 2. Inconscient était déjà au catalogue.

## Octobre 2026 — Description des états sans limite

La description d’un état (Pesanteur, Empoisonné, etc.) n’est plus bloquée à 255 caractères. On peut y écrire un texte aussi long que nécessaire.

## Octobre 2026 — Barèmes en budget ± et formules

Les bonus de sort, consommable et équipement avaient déjà des bornes symétriques (−N…+N), mais le tableau n’affichait que le côté positif. Les cellules s’affichent maintenant en ±N quand c’est un budget. Environ 190 barèmes passent en formule + coefficients (comme les dégâts) ; les résistances % encore quasi vides restent manuelles.

## Octobre 2026 — Barèmes : plus d’onglet Calcul

Dans l’outil **Barèmes**, l’onglet Calcul (extras sort : dés, budget tour, conversion d’une valeur) faisait doublon avec le tableau des normes, dont les cellules affichent déjà les équivalents en dés. Il est retiré. Restent Barème, Conversion (si elle existe) et Propriétés. L’onglet Calcul du panneau MJ d’une fiche créature (décomposition base / équipement / contexte) est inchangé.

## Octobre 2026 — Alertes npm (Dependabot)

Les 8 failles npm (undici, braces via csso-cli, brace-expansion, postcss-selector-parser) sont corrigées : plus d’override qui clouait undici 6.28.0, minification CSS via l’API `csso` à la place de `csso-cli`, et versions patchées pour les autres. `pnpm audit` ne remonte plus de vulnérabilité. Composer n’en avait aucune.

## Octobre 2026 — Audit des caractéristiques

Vérification de toutes les caractéristiques (formules, chartes, coefficients, bornes). Corrections : une créature sans valeur saisie (PA, Agilité…) reprend sa valeur par défaut au lieu de 0 ; les chartes générées respectent désormais le min/max (bonus de maîtrise, bonus de touche, PM) ; la distance de saut peut monter jusqu'à 13 cases ; la réserve de Wakfu peut valoir 0 ; les conditions « touche automatique » des sorts de dégâts/soin/bouclier ne bloquent plus l'enregistrement.

## Octobre 2026 — Coefficients de palier pour les chartes

Sur chaque fiche caractéristique (admin), une formule de génération peut remplir automatiquement le tableau des normes (5 paliers × 20 niveaux). On peut aussi saisir des **coefficients** pour Très faible / Faible / Fort / Très fort (Neutre = 1) : la formule définit le palier neutre, les autres lignes en découlent. Les dégâts de sort (`dommages_spell`) passent à environ 1d6 au niveau 1 et 4d10 au niveau 20, avec montée rapide puis ralentissement. Soin, bouclier, PV temporaires et vol de vie suivent automatiquement.

## Octobre 2026 — UI caractéristiques unifiée (fiche joueur + panneau MJ + Barèmes)

Une seule source de données pour l’aide caractéristique : l’API **card** complète descriptions, défaut et formule affichée. Côté joueur, `CharacteristicInfoPanel` remplace les anciens tooltips / panels dispersés (propriétés, kref CMS, effets, booléens). Côté MJ, `CharacteristicGmPanel` regroupe barème, calcul (avec couches, sur une fiche créature), conversion et propriétés ; le bouton d’aide sépare correctement groupe et entité. L’outil **Barèmes** (menu Outils) couvre les cinq groupes ; les dés s’affichent au survol des cellules du tableau.

## Octobre 2026 — Bonus éphémères et limites absolues

Les formules créature listent désormais tous leurs termes : caractéristique de base, équipement, consommable et sort. Les bonus de consommable et de sort sont **éphémères** : nuls hors combat, ils s'ajoutent sans jamais être plafonnés, ce qui prépare un futur outil de combat. Certaines caractéristiques ont une **limite absolue** sur leur partie permanente : modificateurs (max +7, +10 pour les monstres), compétences et sauvegardes (±15, ±20 pour les monstres), bonus de maîtrise (max 6). Les autres restent souples. Les formules affichées dans les infobulles sont réécrites en texte court (« mod. Int + équip. »), et la décomposition indique les effets temporaires et le plafonnement.

Correctifs liés : le panneau de décomposition (clic sur une caractéristique d'une fiche monstre ou PNJ) s'affichait vide, il montre de nouveau le calcul, avec des libellés courts pour les termes (« Acr. (maîtrise) », « Acr. (équip.) »). L'enregistrement d'un monstre, d'un PNJ ou d'une créature plantait depuis le passage des maîtrises au groupe Profil ; les maîtrises du monstre sont de nouveau enregistrées.

Polish : l'admin liste les termes éphémères détectés dans la formule ; une règle qualité signale une formule créature sans `*_object` quand un bonus objet existe ; la réserve Wakfu lit ses bornes 1–9 depuis `min`/`max` plutôt qu'en dur.

Nettoyage : les fiches Vue ne recalculent plus les statistiques, compétences ou limites et utilisent uniquement le runtime serveur. Les caractéristiques calculées sont découvertes depuis les définitions, sans liste parallèle. Toutes les formules renseignées ont maintenant une formule d'affichage ; celles des créatures restent courtes et masquent les effets éphémères. Les gabarits IA prennent l'initiative, la touche et la CA dans leurs barèmes respectifs.

## Octobre 2026 — Nature des caractéristiques, Profil et Consommable

Chaque caractéristique a une **nature** (descriptif, intrinsèque, bonus/malus, buff/débuff/vol, valeur d'action), indépendante de son groupe. Les bonus et buffs pointent l'intrinsèque créature qu'ils modifient. Deux nouveaux groupes : **Profil** (maîtrises de compétences et de sauvegardes pour classe, spécialisation et monstre) et **Consommable** (effets temporaires). Les ressources sortent du système ; équipements et panoplies restent ensemble. Les formules créature affichent explicitement le bonus d'équipement. L'admin replie par défaut les panneaux Conversion et Mapping.

## Octobre 2026 — Formules de conversion sans bornes internes (buff / débuff)

Les formules de conversion ne portent plus les plafonds : ce sont les `min`/`max` du pivot qui bornent. Elles sont **signées** (`+` buff, `−` débuff) pour les sorts (dégâts fixes, résistances, caracs, etc.). Plafond dégâts sort **±80**. Aperçu graphique admin : bornes d’affichage corrigées.

## Octobre 2026 — Équilibrage Excel via formules de normes

Les barèmes (dégâts, soin, bouclier, PV temporaires, vol de vie, retraits PA/PM, placement, consommables) sont générés par des formules dans le système de caractéristiques (`norms_formula` → grille). Les dégâts suivent coef × PV du palier ; le vol de vie vaut 30 % des dégâts ; les invocations reprennent les normes monstre. Un hub **Équilibrage** dans la gestion du contenu pointe vers chaque fiche. Conversion classique et IA (`NpcStatGabarit`) lisent la même source. Remise à zéro : seed overwrite puis `characteristics:regenerate-norms`.

## Octobre 2026 — Barèmes de sorts et conversion en dés

Les tables de référence dégâts / soins / bouclier / PV temporaires / vol de vie restent des repères (dés, budget tour) intégrés à l’outil générique **Barèmes** (menu Outils et sections CMS). Ce sont des repères, pas une loi absolue.

## Octobre 2026 — Suppression de sort sans « Page introuvable »

Après suppression depuis l’édition, le bouton Précédent du navigateur ne rouvre plus la fiche déjà mise en corbeille (404). Les liens des notifications pointent vers les bonnes URLs `/entities/…`, et après suppression vers la liste plutôt que la fiche disparue. Depuis une modal d’édition, la suppression reste sur la liste.

## Octobre 2026 — Édition d’entité : header compact et grille deux colonnes

Toutes les fiches d’édition (équipements, monstres, PNJ, classes, panoplies, etc.) reprennent le header compact des sorts : retour, titre vers la fiche, état, options, annuler, supprimer et enregistrer restent visibles en haut. Le formulaire s’affiche en deux colonnes ; les métadonnées sont repliées par défaut. Les blocs de relations (recette, sorts, traits…) restent dans des panneaux séparés sous le formulaire. Les capacités gardent leur grille dédiée, avec le même header.

## Octobre 2026 — Enregistrement des sorts fiable (localhost / 127.0.0.1)

Le toast « Erreur lors de la sauvegarde » sans détail venait d’un écart d’adresse : le site ouvert en `127.0.0.1` alors que les routes JS pointaient encore vers `localhost`. L’enregistrement partait « ailleurs » et échouait sans message utile. Les routes suivent maintenant l’adresse réellement utilisée dans le navigateur.

## Octobre 2026 — Édition des sorts : props globales et header

Éléments, type de ciblage et disponibilité rituelle appartiennent au sort, plus aux degrés. Les propriétés communes restent éditables dans un container toujours visible mais replié. La portée de jeu d’un effet n’est plus cachée dans « Avancé ». Disposition en deux colonnes (descriptif / hors degrés, puis communes / métadonnées repliés, puis degrés & effets). Header compact : retour et porteurs à gauche, titre cliquable au centre, aide formules / état / options / annuler / supprimer / enregistrer à droite.

## Octobre 2026 — Édition des sorts condensée

L’édition d’un sort est regroupée en blocs (description, propriétés hors degrés, lancement, degrés & effets, métadonnées). Les degrés restent en onglets : un nouveau degré copie le précédent. On peut faire hériter tout le bloc de propriétés du degré précédent ou du sort de base. La zone se choisit par icônes de formes avec aperçu. Chaque effet a une valeur normale et une valeur critique. Les porteurs du sort (monstres, PNJ, classes) s’ouvrent rapidement avec un aperçu, et la source Dofus est accessible depuis l’éditeur. Un seul bouton enregistre le sort et tous les degrés.

## Octobre 2026 — Degrés de sorts simplifiés

L’édition d’un sort ne passe plus par plusieurs « définitions d’effet ». On ajoute des **degrés** (niveaux) : chacun porte les propriétés de lancement (PA, portée, ligne de vue, zone, relance, limites et incantation) et des **effets** (frapper, soigner…). Un degré peut reprendre les effets du précédent pour éviter de tout ressaisir. Les effets proposent le bon outil selon l’action : élément, caractéristique recherchable, créature ou état, avec une durée propre. La portée se saisit simplement sous la forme `4` ou `2-8` et la zone retrouve son aperçu et son aide de notation. Le ciblage, la résolution et l’utilisation en réaction restent des propriétés globales du sort. À l’affichage, des onglets de niveau mettent à jour les propriétés visibles. L’éditeur montre aussi les monstres, PNJ et classes qui utilisent le sort.

Supprimer le premier niveau (celui qui porte les effets) ne vide plus les onglets restants : les effets sont recopiés sur le degré suivant s’il héritait.

## Octobre 2026 — Sauvegardes ZIP fiabilisées

Une sauvegarde produit désormais une seule archive ZIP horodatée (base, médias `storage/app`, contenu `private/game`, manifeste avec checksums). On peut lister, supprimer et restaurer depuis la ligne de commande et la page admin Sauvegarde, avec confirmation renforcée et sauvegarde de secours avant restauration. Le cron configurable `project_backup` reste disponible ; le verrou empêche deux opérations en parallèle. Le suivi de restauration reste visible pendant la maintenance, et un crash / reboot marque l’opération comme interrompue puis nettoie les restes. Les médias ne sont plus recopiés dans un dossier temporaire avant compression (évite les plantages mémoire sous WSL), et le dossier des archives n’est jamais ré-inclus dans une sauvegarde.

## Octobre 2026 — Nettoyage des caractéristiques

Suppression des caractéristiques en trop (compétences Artisanat / Herbaliste / Connaissance des créatures, échec critique d’équipement, puissance et poussée de sort, dommages et résistances sagesse·vitalité). Les bornes numériques créature / objet / sort sont symétriques (`min = −max`), avec recalcul des plafonds monstre. Les libellés parlent de **dommage** fixe. Les règles et la doc technique suivent.

## Octobre 2026 — La base garde les modifications

Les changements faits dans l’application (caractéristiques, pages seedées, fiches versionnées…) ne sont plus écrasés au redémarrage du serveur. Les fichiers de seed ne remplissent que ce qui manque. Une sauvegarde hors production recopie la base vers ces fichiers. Pour forcer le retour aux fichiers du dépôt : `project:seed --overwrite`. Les boutons d’export/import seeders ont été retirés de l’admin (caractéristiques, IA métier) : seul l’écran Sauvegarde pilote cette copie, pour éviter d’écraser des données par erreur.

## Octobre 2026 — Caractéristiques créature validées, monstres plus souples

Toutes les caractéristiques de créature sont passées en **validée**. Les min/max des **monstres** s’élargissent de 50 % de part et d’autre (un score 6–24 va de -3 à 33) ; personnages et PNJ gardent les bornes d’origine.

## Octobre 2026 — Système de caractéristiques finalisé

Les définitions d’équipement sont reproductibles (emplacements versionnés). La réserve de Wakfu, les PV max d’objet et les invocations suivent les règles. Les bonus d’équipement se lisent dans `effect` (le brut Dofus reste dans `bonus`). On peut éditer totaux et contexte sur les fiches monstre/PNJ. L’Intimidation choisit Force ou Chance ; les expertises sont plafonnées à 3. Les tooltips de caractéristiques sont utilisables au clavier.

## Octobre 2026 — L’Essentiel pour qui vient de D&D

Une page **Si tu viens de D&D** dans L’Essentiel résume les écarts avec la 5e : caractéristiques, PA/PM, tacle, Wakfu, classe et spécialisation, classe d’armure. Le sommaire de Bien démarrer et l’accueil (rubrique « Déjà à l’aise en JdR ») y renvoient.

## Octobre 2026 — Sauvegardes et files séparées

Lancer une sauvegarde, un import ou une compilation ne reste plus bloqué derrière les alertes. Chaque tâche a sa propre file et démarre toute seule. Les alertes (connexion, fiche modifiée, maintenance) arrivent dans le centre de notifications au lieu de s’accumuler en silence.

## Octobre 2026 — Contenu pour les MJ, Admin pour l’app

La gestion du contenu est ouverte aux maîtres du jeu (langues, types, caractéristiques, effets), après confirmation du mot de passe. Import DofusDB, mappings et IA restent réservés aux admins. Les astuces de chargement sont dans l’espace administration. Débloquer son mot de passe n’élargit jamais le rôle.

## Octobre 2026 — Astuces sur l’écran de chargement

Pendant le chargement, des phrases courtes défilent en bas de l’écran, une par une, avec un fondu. Certaines ouvrent Discord ou GitHub. Un admin les ajoute, modifie ou retire depuis **Astuces de chargement**, peut en mettre en avant pour qu’elles apparaissent plus souvent, et régler la durée d’affichage de chaque phrase.

## Octobre 2026 — Infobulle « Lancer » dans la recherche

Dans la recherche, l’infobulle du dé (Lancer) passe devant le champ et la croix. Son fond n’est plus découpé.

## Octobre 2026 — Alertes en verre teinté

Les bandeaux d’alerte ne sont plus un aplat de couleur. Le fond laisse voir la page derrière, avec un flou et une teinte selon le type (info, succès, attention, erreur). Le texte reste clair et lisible.

## Octobre 2026 — Enregistrer une page longue sans tout perdre

Sur une page de règles un peu longue, les chapitres plus bas mettent un moment à s’afficher. Enregistrer trop tôt n’efface plus leur texte : on attend qu’ils soient là, puis on peut modifier sans perdre le reste.

## Octobre 2026 — Journal du jeu

La page **Journal** dit en peu de mots ce qui est déjà jouable, et une frise indique la suite : campagnes, scénarios et fiches en 1.4, puis des outils de combat en 1.5. Les textes restent des fichiers markdown. Un admin les modifie depuis la page.

## Octobre 2026 — Réglages IA : enregistrer le prompt, choisir un étalon

Sur **IA métier**, un étalon qui n’est pas une fiche jouable bloque tout l’enregistrement, y compris le prompt superviseur. La page les liste et propose de les retirer. Le sélecteur d’exemples suit l’onglet (consommable, objet, sort…) et retrouve une fiche par son nom ou son identifiant officiel, les plus récentes en premier.

## Octobre 2026 — Classe d’armure : le modificateur

La classe d’armure est 10 + le modificateur de Vitalité, plus le bouclier ou l’équipement. La note de conversion et le guide de création des monstres disaient encore d’ajouter le score. Ils disent maintenant le modificateur, comme la fiche de caractéristique et L’Essentiel.

## Octobre 2026 — Enregistrer une caractéristique sans redescendre

Sur une caractéristique, le bouton **Enregistrer** est dans la barre du haut, à côté du nom, de l’état et de **Lier**. Il s’allume seulement après une modification, et la barre reste visible quand on descend dans la fiche. Sur un petit écran, le libellé « Lier » et la clé technique se rangent pour garder la barre sur une ligne.

## Octobre 2026 — L’Essentiel aligné sur le livre

L’aide-mémoire reprend les formules du livre là où il les raccourcissait : initiative (modificateur d’Intelligence et équipement), réserve de Wakfu (maîtrise + équipement, max +3), perception passive, bonus d’objet (dégâts et CA jusqu’à +5, caractéristique plafonnée à +4 et +8 au total) et emplacements (deux anneaux, Dofus sans quota).

## Octobre 2026 — Bornes et bonus séparés

Sur une caractéristique dans un texte, l’infobulle distingue le personnage ou le PNJ, la créature, le bonus d’équipement et l’effet de sort. Le barème d’un objet ne s’affiche plus comme s’il était le minimum et le maximum de la créature.

## Octobre 2026 — Formules en paliers

Quand une caractéristique dépend d’une autre par paliers (par exemple les PM selon le niveau), l’infobulle et la charte montrent un tableau : de tel niveau à tel niveau, telle valeur. Le JSON technique n’apparaît plus.

## Octobre 2026 — Références dans les textes de règles

Dans une page de règles, un mot comme PA, un nom de capacité ou un renvoi vers une autre page redevient une puce : au survol, le résumé de la caractéristique, la fiche courte de l’entité, ou le titre et un extrait de la section. On peut toujours en insérer avec `@` dans l’éditeur de texte. Les fichiers de règles ne doivent plus glisser un lien à l’intérieur du libellé d’un autre (ça cassait les tableaux).

## Octobre 2026 — Chapitres des classes et spécialisations

Sur une fiche de classe ou de spécialisation, un visiteur ne voit plus les textes encore en brouillon ni ceux réservés aux meneurs. C’est la même idée que pour les sorts accrochés à la fiche. La page Modifier continue de tout montrer, pour ne rien décrocher par erreur.

## Septembre 2026 — Tooltips et alertes plus lisibles

Les bulles d’aide (lancer de dés dans la recherche, flèche pour replier l’en-tête) se placent correctement et se lisent mieux. Les bandeaux d’alerte reprennent le fond des petites fiches, avec un trait et une ombre de la couleur du message.

## Septembre 2026 — Droits d’accès : plus d’écrasement ni de raccourci MJ

Enregistrer un monstre ne vide plus ses seuils de lecture / écriture. L’édition multiple (objets, campagnes, etc.) n’autorise plus un meneur à changer ces seuils : seuls les admins le font, comme sur le formulaire d’une fiche.

## Septembre 2026 — Bouclier : pas contre chute ni poussée

Les **points de bouclier** n’encaissent pas les dégâts de **chute** ni de **poussée** : ce sont les PV temporaires, puis les PV. Règles (3.2.4, 2.2.2, chutes), L’Essentiel et les aides caractéristiques sont alignés.

## Septembre 2026 — En-tête fiche entité mobile (nom puis image)

Sur téléphone, le nom de la fiche est au-dessus de l’image (plus collé à droite à côté d’une grosse vignette). Dès tablette, l’image revient à gauche.

## Septembre 2026 — Fiches entité mobile : tooltips et description

Sur téléphone, les infos-bulles des propriétés d’une fiche ne restent plus bloquées à l’écran : un appui ouvre, un appui ailleurs ferme. La description s’affiche en entier (retour à la ligne), plus tronquée au milieu.

## Septembre 2026 — Dock mobile branché (menu, recherche, compte)

Sur téléphone, les trois boutons du menu bas fonctionnent : **Menu** ouvre la navigation, **Recherche** lance la recherche globale, **Compte** ouvre le menu utilisateur (ou connexion / inscription).

## Septembre 2026 — JSON injectable sur toutes les entités

L’onglet **JSON** (texte ou fichier) n’est plus réservé aux 5 types convertibles par IA. Tout admin peut l’utiliser sur n’importe quelle fiche du registre : même validation/sanitisation, écriture en **auto**, tableau avant/après. Les sorts / PNJ / etc. gardent le schéma IA ; les autres types utilisent les champs fillable (sans toucher state, droits, etc.).

## Septembre 2026 — Couverture tests inject JSON (5 types)

Les Feature PHPUnit d’injection couvrent sort, objet, consommable, rencontre et PNJ (sans appel Anthropic). Côté front : onglet JSON du modal Sources + `submitAiInject` du dispatcher.

## Septembre 2026 — Injecter un JSON comme paquet IA

Sur **Sources**, un onglet **JSON** (à côté de DofusDB et Conversion IA) permet de coller ou déposer un fichier au format de réponse de l’IA. Même contrôles (admin, mot de passe), même validation, même écriture en **auto**, puis le tableau avant/après — **sans** appeler Anthropic. À la création d’une fiche, le même flux existe via l’onglet JSON du modal Créer.

## Septembre 2026 — Responsive chrome et tableaux

Sur téléphone (&lt; 768px), le menu bas et le contenu ne se marchent plus dessus (padding, FAB, cookies). Les filtres des bibliothèques d’entités s’ouvrent dans un tiroir latéral ; les modes de vue passent en icônes. Les tableaux s’adaptent à la largeur réelle du bloc (container queries), pas seulement à celle de l’écran.

## Septembre 2026 — Perf : boot, Règles, édition, save

Lancement plus léger (vues d’entités en lazy, caractéristiques hors chemin critique, splash plus court). Pages **Règles** : HTML des sections différé + lecture sans TipTap. Édition : plus de dump monstres/ressources/conditions dans les props. Enregistrement : on reste sur le formulaire (`back`) au lieu de recharger toute la fiche show.

## Septembre 2026 — TipTap 3 et Vitest 4

L’éditeur riche passe en **TipTap 3** (extensions regroupées, signature `setContent` à jour). Les tests front tournent sous **Vitest 4.1.11** (pool forks). Les alertes de sécu TipTap / Vitest côté npm sont résolues.

## Septembre 2026 — Finalisation technique avant reprise du contenu

Audit sécu / simplicité / tests / ops / perf, correctifs appliqués et **tout mergé sur `main` GitHub** : droits (usages d’effets, création user, OAuth), scan secrets à chaque push, catalogues d’édition plus légers, policies admin factorisées. Disque MJ, collision scrap monstres et fiches perso aussi intégrés. Reste manuel : rotation du mot de passe MySQL (fuite historique), upgrade TipTap major plus tard.

## Septembre 2026 — Filtres d’effets sur consommables et ressources

Les consommables (et ressources) se filtrent maintenant comme les équipements : on choisit une caractéristique, puis une valeur min/max. Les soins, PV temporaires et points de bouclier sont enregistrés en chiffres (plus seulement dans le texte), avec le détail d’usage (hors combat, durée…) toujours lisible sur la fiche.

## Septembre 2026 — Icônes de classes toujours visibles dans le menu

Dans le menu latéral, les symboles de classes restent **visibles au repos** (niveaux de gris) et **se colorisent au survol** (ou quand l’entrée est active). Avant, seuls les symboles N&B étaient utilisés : trop sombres, ils disparaissaient — sauf l’Osamodas, qui n’avait que le symbole couleur.

## Septembre 2026 — Aptitudes / capacités et plafonds de carac

**Façonneur de sorts** (Érudit) devient capacité garantie du palier 9 ; l’aptitude du palier est **Mémoire des formules**. Les autres aptitudes contextuelles / réactives (ex. Insaisissable) restent si elles sont logiques sans coût. Les formules de **modificateurs** suivent enfin les règles : plafonnées à **⌊niv/4⌋+2** (absolu **+7**). Score absolu **24** (le **20** n’est que le plafond du niveau 12).

## Septembre 2026 — Même gabarit pour les onze spécialisations

Les **onze** fiches ont maintenant la même structure : **5 compétences** au niveau 1 (on en prend 3), et **2 options** de capacité à chaque palier. Les aptitudes ne bougent pas. L’équilibrage des effets viendra après.

## Septembre 2026 — Onze spécialisations jouables, dix-neuf classes présentées

Les **cinq** spécialisations qui restaient en brouillon (**Artisan·e, Négociant·e, Sylvain·e, Marin·e, Courtisan·e**) sont écrites comme l’Érudit : sept paliers, choix entre garanties et options, trois aptitudes, vrais PA, plus de « à calibrer ». Les **onze** fiches tiennent dans le dépôt.

Les **19 classes** ont une vraie présentation (comment on joue, passif cliquable, ce que la classe n’est pas) à la place de la liste « Capacités disponibles pour ce palier ». Passif, voies et sorts restent sur la fiche.

## Septembre 2026 — Capacités dans le texte : vraie fiche minimale

Dans une section (palier de spécialisation, règles…), survoler une **capacité** (ou un sort, un objet…) ouvre la **même fiche minimale** que partout ailleurs — plus la petite carte nom + « Magie (Wakfu) ». Le clic continue d’ouvrir la page.

## Septembre 2026 — Choix entre X et Y, plus de points via la spé

Les paliers de spécialisation n’écrivent plus « emplacement vide » : on lit **choix entre X et Y**, avec les vrais noms. On prend des **aptitudes**, des **capacités**, des **traits**, des **compétences** et des **métiers**. Plus de **+2 points** de caractéristique : c’était trop fort. Les points restent ceux de la création et des six niveaux fixes.

## Septembre 2026 — Les six spécialisations jouables sur le gabarit Érudit

**Milicien·ne, Dévot, Artiste, Explorateur·rice et Voleur·euse** sont écrites comme l’Érudit : sept paliers, une capacité garantie plus une option, trois aptitudes, plus de liste D&D à rallonge. Chaque fiche tient dans le dépôt.

## Septembre 2026 — Les 19 classes sur le gabarit Iop

Les **18 autres classes** ont le même découpage de sorts que l’Iop : au niveau 1, jusqu’à **quatre variantes** au choix 1 (dégâts ou soins élémentaires), puis **un primordial** au choix 2 et un au choix 3. Les 9 choix suivants tombent aux vrais emplacements (**2, 4, 5, 6, 7, 8, 10, 11, 12**). Plus de sorts tagués 13 ou 14 : à partir du 12, on intensifie. Sram garde Invisibilité dès le niveau 1 ; Zobal ouvre sur Cavalcade avant Plastron.

## Septembre 2026 — Voies, passifs et modèle Iop / Érudit

Les **19 classes** ont leurs **quatre voies** (Feu, Terre, Eau, Air) calées sur le tableau *Généralités Classes* : l’Iop n’est plus « tout dégât », le Feu améliore, l’Eau protège, l’Air place. Chaque classe n’expose plus que son **passif** (Fureur pour l’Iop, visible sur la fiche). Les sorts de dégâts élémentaires de l’Iop sont au **même niveau** : quatre variantes au choix 1, Bond et Concentration chacun tout seuls. Les **12 choix** sont numérotés dans l’interface.

Sur une **spécialisation**, une seule section par palier : les capacités s’ouvrent au clic dans le texte, comme les jets de sauvegarde et les compétences. Plus de doublon « Niveau 1 » / « Niveau 1 (Capacité) ».

## Septembre 2026 — Créer une fiche depuis les Bibliothèques

Sur les pages **Bibliothèques** (tableaux d’entités), un bouton **Créer** apparaît pour les admins. Il ouvre un petit formulaire : nom, description, et deux ou trois infos utiles — pas toute la fiche. On peut aussi lancer une **conversion IA** (même droit admin, mot de passe). Ensuite la fiche s’ouvre déjà en **Modifier**, pour compléter le reste.

## Septembre 2026 — Érudit modèle et sorts Iop jusqu’au niveau 12

L’**Érudit** est la première spécialisation réécrite sur le gabarit verrouillé : sept paliers, une capacité garantie plus une option, trois aptitudes (Politicien, Mémoire des formules, Expertise en Wakfu). **Façonneur de sorts** est une capacité garantie du palier 9 (effet lié au lancement de zone), pas une aptitude. Plus de liste D&D à rallonge.

Les **sorts Iop** s’apprennent maintenant aux vrais emplacements (2, 4, 5, 6, 7, 8, 10, 11, 12). Chaque fiche porte ses lignes d’intensification : au niveau 13, 16 et 20, le même sort gagne un dé — ou une case, ou rien (« — ») s’il est déjà au plafond.

## Septembre 2026 — Conversion IA des PNJ : classes, spés et noms Dofus

Quand on convertit un PNJ par IA, le modèle **voit** les classes et spécialisations (même en brouillon) et les sorts jouables **par classe**, même si la fiche n’avait pas encore de classe. Il invente un nom façon calembour Dofus et une courte description collée au kit. Les stats manquantes suivent encore le gabarit ; si la carac de voie n’est pas donnée, elle est déduite de l’élément des sorts. On n’exige pas qu’une spécialisation soit déjà jouable.

## Septembre 2026 — Intensification des sorts et gabarit des fiches

À partir du niveau 12, plus de nouveau sort de classe : les 12 déjà connus grandissent aux niveaux **13, 16 et 20**. On lit une nouvelle ligne sur la même fiche (plus de dés, de portée ou de durée), pas un 13ᵉ sort. Si la fiche n’a pas encore ces lignes, on ajoute un dé — plafonné à 5d6 (attaque) ou 5d4 (soin). Les spécialisations s’écrivent toutes sur le même gabarit à 7 paliers : 1 capacité garantie, 1 option, 1 compétence (2 seulement si c’est écrit), 3 aptitudes aux niveaux 3, 9 et 15.

## Septembre 2026 — Refonte de la progression : moins de points, plus de choix

La montée en niveau change de forme. **Les sorts sont tous acquis au niveau 12** : 3 au niveau 1, puis un de plus aux niveaux 2, 4, 5, 6, 7, 8, 10, 11 et 12, soit 12 sorts. Au-delà, ils ne font que s’intensifier — on joue rarement jusqu’au 20.

Les **caractéristiques** partent toutes à 8 ; on répartit 10 points à la création (12 en descendant une carac à 6). Le plafond porte sur le score : **14 + ⌊niveau / 2⌋** (absolu **24** au niv. 20 — le **20** n’est que le plafond du niv. 12). Le modificateur est plafonné à **⌊niv/4⌋+2** (absolu **+7**). Ensuite, seulement **6 points offerts** (niveaux 2, 4, 8, 10, 14 et 16). Les **équipements** ne peuvent plus dépasser **+8 par caractéristique**, forgemagie comprise (max +2).

Les **spécialisations** montent aux niveaux **1, 3, 6, 9, 12, 15 et 20**. À chaque palier : un **choix entre** les capacités garanties, un **choix entre** les options (capacités, ou un **trait** à partir du niveau 12), plus compétences et métiers. Pas de points de caractéristique.

Au passage, **aptitude et capacité échangent leur sens**. Une **capacité** est une action qui ressemble à un sort, choisie à chaque palier. Une **aptitude** est un bonus (souvent passif) que la spécialisation donne **sans choix**, aux niveaux 3, 9 et 15 — trois en tout, pas plus.

## Septembre 2026 — Les onze spécialisations recalées sur les nouveaux paliers

Les six spécialisations jouables (Artiste, Dévot, Érudit, Explorateur·rice, Milicien·ne, Voleur·euse) étaient écrites sur une vieille grille de dix paliers. Elles sont **redécoupées sur les sept paliers** actuels : les gains des niveaux 13 et 15 fusionnent, ceux des niveaux 18 et 20 aussi, et les expertises retombent sur les paliers 9, 15 et 20 comme le veut la règle. Chacune garde **trois aptitudes**, aux niveaux 3, 9 et 15 ; les autres bonus nommés redeviennent des capacités proposées à leur palier. Les cinq brouillons (Artisan·e, Négociant·e, Sylvain·e, Marin·e, Courtisan·e) sont réécrits sur la même trame.
## Septembre 2026 — PDF MJ hors disque public

L’atelier MJ compilé n’est plus écrit sous `storage/app/public` : un joueur ne peut plus le récupérer via `/storage/downloads/generated/…`. Le téléchargement passe uniquement par `/telechargements/mj-pdf` (rôle MJ). Une copie publique déjà générée est migrée puis effacée.


## Septembre 2026 — Règles sans catalogues, changelog hors livre, PDF MJ

Le livre joueur ne recopie plus les fiches (classes, sorts, monstres…) : il renvoie aux **Bibliothèques**. Le **changelog** n’est plus une annexe de règles : il reste la page Informations. Les meneurs ont un **PDF d’atelier** à part (**Pour les MJ → Ressources MJ**), avec le calibrage ; la création de fiches se fait sur le site (Création).

## Septembre 2026 — Catalogue JDR en relecture (`auto`)

Les sorts de classe, monstres du bestiaire, invocations, équipements étalons, consommables JDR et PNJ Incarnam ne sont plus marqués **jouables** au seed : ils arrivent en **auto**, à relire. Un `project:seed` ne les republie plus tout seuls.

## Septembre 2026 — Équilibrage des entités dans Pour les MJ

Les tables de calibrage (classes, sorts, objets, loot, économie…) ne sont plus dans le menu Règles : elles passent dans **Pour les MJ**, visibles seulement des meneurs. Les joueurs gardent les téléchargements (PDF, fiche, logo) sous **Règles → Ressources**. Le PDF du livre ne contient plus ce chapitre.

## Septembre 2026 — Livre de règles PDF plus court à l’impression

Le PDF n’ouvre plus une page blanche à chaque petite fiche : une coupure par grande partie, format A4, police plus petite. Les listes de sources et deux annexes d’historique (choix de design, archives) restent sur le site / dans les fichiers, pas dans le livre à télécharger.

## Septembre 2026 — Couleurs d’état plus distinctes

Les pastilles d’état ne se confondent plus : **Brut** reste rouge, **Jouable** vert, **Brouillon** passe en **umber**, **Auto** en **indigo**, **Archivé** reste cyan. Ce sont des variables `--color-state-raw`, `--color-state-draft`, etc., utilisées partout (recherche, tableaux, points, badges).

## Septembre 2026 — Couleurs d’état dans la recherche globale

Les pastilles **Jouable / Brouillon / Brut / Auto / Archivé** (barre de recherche et filtres des tableaux) ont un fond **foncé** si elles ne sont pas cochées, et la **couleur de l’état** (plus claire que le point) si elles le sont.

## Septembre 2026 — Formules de dés dans la recherche et l’outil

On peut taper une formule dans la **barre de recherche** (`d12`, `3d8+2`, `[2-6]`, mais aussi `50-17` ou `1+4×7`) : une bande affiche min / moyenne / max, l’équivalent en dés des tranches, **Valeur :** du lancer et une icône sans contour (ombre au survol, tooltip « Lancer »). Un nombre seul (`12`) reste une recherche. Chaque jet s’ajoute en historique (`formule = valeur`), uniquement en mémoire : fermer la recherche ou l’outil l’efface. L’outil **Lanceur de dés** reprend la même bande, avec une courte explication et les raccourcis d4–d100. Les tranches se convertissent exactement (`[2-6]` → `1d5+1`).

## Septembre 2026 — Skeletons de chargement et case de sélection minimale

En attendant les fiches, on voit **plusieurs cartes** (image + titre), plus un seul pavé gris. Les sections de page ont aussi un skeleton qui ressemble au contenu (texte, tableau, galerie…). Sur une carte **minimale**, la case à cocher n’apparaît **que déployée** (ou si elle est déjà cochée).

## Septembre 2026 — Sources : scrap d’une fiche existante

**Sources** peut scrap une fiche qui a déjà un id DofusDB, même si son type n’est pas coché pour l’import de masse. Sur une fiche **jouable**, « forcer » est coché comme pour l’IA.

## Septembre 2026 — Sources : deux onglets, image, mix avant/après

Sur une carte minimale, **Sources** rouvre les deux onglets : conversion **DofusDB** (algo) et conversion **IA**. On coche si on veut **récupérer l’image**. Après l’écriture, le tableau compare ancien et nouveau : un clic sur une cellule (ou sur l’en-tête de colonne) choisit ce qu’on garde. **Enregistrer** applique ce mix ; **Rétablir** (ou fermer) annule toute la conversion.

## Septembre 2026 — Largeur des pages et des sections

Les blocs de page ne se collent plus au contenu : texte et images occupent les **deux tiers** de la zone (centrés), les **tableaux** prennent toute la largeur dès l’affichage (plus de carte étroite qui s’élargit au chargement). Si un tableau dépasse, on **défile horizontalement** dans la section.

## Septembre 2026 — Conversion IA : kit PNJ et gabarit monstres

Une conversion **PNJ** refuse deux objets sur le même emplacement (deux capes, etc.) et complète les stats manquantes avec le gabarit de niveau. Une conversion **rencontre** reçoit le même gabarit dans le prompt, pour coller les sorts aux PV / PA de la fiche. Les objets Dofus scrapés restent hors scope : l’IA ne retravaille que les uniques. Sur **IA métier**, un enregistrement incomplet écrasait les étalons du dépôt : on peut **réinitialiser** pour retomber sur le fichier.
## Septembre 2026 — Scrap monstre : ne plus écraser le bestiaire JDR

Importer un monstre DofusDB qui porte le même nom qu’une fiche du **bestiaire** ou une **invocation** (Bouftou, Tofu, Chafer…) ne réécrit plus cette fiche jouable. Le scrap crée une **nouvelle** entrée Dofus (état raw), l’étalon JDR reste intact.

## Septembre 2026 — Conversion IA : pas d’écrasement silencieux

Une conversion IA ne peut plus viser **un autre type** que la fiche ouverte (l’action doit coller à l’URL). Une fiche **jouable ou archivée** exige une confirmation (`force` / `--force`), comme DofusDB : les exemples de commandes sur Ganymède ou le Piou Vert ne les écrasent plus par accident.

## Septembre 2026 — L’IA derrière le même cadenas que la gestion admin

La conversion IA n’est plus ouverte dès qu’on est admin. Il faut **confirmer le mot de passe** (comme pour la gestion admin) : ensuite le compte reste déverrouillé un moment, et on peut lancer des conversions sans retaper le mot de passe. Les MJ, joueurs et invités n’y ont pas accès. La page **IA métier** demande aussi ce déverrouillage pour la consulter.

## Septembre 2026 — Modèle Haiku et cache prompt

Sur **IA métier**, on choisit le modèle Anthropic (Haiku 4.5 par défaut, le moins cher, ou Sonnet / Opus). Le **cache prompt** est coché : les règles et exemples ne sont pas refacturés à chaque fiche. On peut le décocher pour déboguer.

## Septembre 2026 — Images des monstres

Les fiches du **bestiaire** et des **invocations de classe** ont maintenant le portrait Dofus (URL `api.dofusdb.fr`, indexée par le `gfxId`, pas l’id de la fiche). Un scrap de monstre DofusDB pose aussi cette URL si le fichier n’arrive pas à se télécharger. Le catalogue et la vue ligne lisent `creature.image`.

## Septembre 2026 — Upload : garder l’image si le fichier est refusé

Remplacer l’illustration d’une ressource, l’icône d’une caractéristique ou un avatar n’efface plus l’ancien fichier **avant** de vérifier le nouveau. Un SVG (ou un envoi vide) est rejeté, et l’image d’origine reste. Les pièces jointes de section n’acceptent plus les SVG (contenu exécutable).

## Septembre 2026 — Prix kamas : plus d’écrasement ni de double compte

Un consommable ou un équipement qui n’avait encore que la colonne `price` (sans part calculée / ajustement) ne passe plus à 0 kamas dès qu’on enregistre autre chose. Dans le catalogue, coller un prix en édition multiple pose le **total affiché**, pas un bonus empilé sur la formule.

## Septembre 2026 — Avant / après après une maj

Après un **scrap DofusDB** ou une **conversion IA**, on ne se contente plus d’un toast. Un tableau compare la version d’avant et la nouvelle (champs changés en surbrillance). **Enregistrer** garde la nouvelle version ; **Rétablir** revient à l’instantané. Si l’IA n’a rien pu écrire (gel, clé absente), le message est honnête : ce n’est plus un faux succès.

## Septembre 2026 — IA métier : un type à la fois

Sur **IA métier**, objet, sort, monstre, PNJ et consommable ne sont plus empilés. On les ouvre comme les **caractéristiques** : une colonne d’onglets à gauche, un seul panneau à droite. Le solde, les prompts globaux et les étalons d’équipement restent sur la page.

## Septembre 2026 — Solde IA visible et panoplies or

Sur **IA métier**, on voit les **tokens du mois** (cette app) et le **crédit Anthropic restant** s’il est connu, plus les estimés par action. Le modal **Sources** affiche le même solde à côté du coût estimé. Les **panoplies exemples** se choisissent comme les fiches (recherche jouable), plus besoin de les coller dans un fichier.

## Septembre 2026 — Ateliers sur la vue d’ensemble

Sur **Gestion du contenu** (`/admin/content`), trois cartes **Récupérer / Mettre à jour / Compléter** ouvrent l’atelier DofusDB (avec `?mode=`), plus une carte **Génération IA** vers `/admin/content/ia-generation`. Les camemberts restent en dessous.

## Septembre 2026 — Atelier Import DofusDB

L’atelier **Gestion du contenu → Import DofusDB** a trois modes : **Récupérer**, **Mettre à jour**, **Compléter**. On choisit l’entité, les filtres, les propriétés (tout ou rien, image comprise) et les relations. La recherche donne un compteur ; l’import part en jobs. **Compléter** ne prend que les IDs DofusDB encore absents. **Mettre à jour** respecte `auto_update` par défaut. Les nouvelles fiches arrivent en **raw**. Plus de mode « images seules » ni de preset tout auto_update sur cette page (la maj unitaire reste sur chaque fiche).

## Septembre 2026 — Étalons IA : on cherche les fiches

Sur **IA métier**, les fiches exemples se choisissent en les cherchant (vivier **jouable** par défaut). On en ajoute ou retire d’un clic ; plus besoin de coller des identifiants.

## Septembre 2026 — Pipeline de conversion IA

On peut lancer une **conversion IA** depuis la fiche (même icône **Sources** que DofusDB) ou en console (`ia:convert` / `ia:convert-encounter`, avec `--user` admin). Ça écrit une proposition en **auto**, jamais jouable. Types : **rencontre** (monstre + deux ou trois sorts), **sort** (texte d’effets), **PNJ** (fiche + kit playable), **objet** unique, **consommable** (effet). Les admins voient le solde Anthropic et un estimé de coût sur la page **IA métier**. L’accès HTTP demande le mot de passe (comme la gestion admin). Les MJ n’ont pas le bouton, pour limiter la facture.

## Septembre 2026 — Leftover règles : équipement +4, index, annexe

Les seeders d’objets (Vitalité, Force, Intelligence, Chance, Agilité, Sagesse) plafonnent à **+4** par pièce, forgemagie **+2**, comme le livre. L’index n’est plus un dump de titres. Le changelog (ex-1.3, un temps annexe 6.1) est la page **Informations**. Aptitudes : listes en 2.4, activation en 3.4, sans recopier les mêmes tableaux.

## Septembre 2026 — Fiches Création (conversion)

L’atelier **Pour les MJ → Création** détaille maintenant sorts, monstres, équipements, consommables, capacités, traits et ressources : philosophie, points à respecter, limites (chiffres et propriétés de fiche), conseils, exemples jouables. C’est le même texte que l’IA de conversion recevra plus tard (`ia:creation-guides`), sans l’appeler encore. Boutiques, langues, campagnes et scénarios attendront.

## Septembre 2026 — Visuels des classes

Chaque classe a ses **symboles** (couleur et N&B), ses **têtes** mâle / femelle et ses **personnages** full. La fiche complète montre le personnage (on peut passer de M à F), les petites cartes montrent la tête (mâle condensé, femelle déployé), et le menu utilise le symbole N&B qui passe en couleur au survol.

## Septembre 2026 — Cinq PNJ d’Incarnam

Les coquilles vides (garde d’Astrub, marchande de Bonta, aubergiste) laissent la place à **cinq fiches jouables** : Ganymède (Xélor érudit), Dathura (Eniripsa dévote), Tabach (Enutrof boulanger), un milicien Iop, Fouduglen le voleur. Classe + spécialisation + quelques objets et sorts. L’atelier **Création → PNJ** pose le cadre (identité, rôle, gabarit, kit 1/slot). Un pré-filtre liste les équipements et sorts jouables à envoyer plus tard à l’IA, sans l’appeler.

## Septembre 2026 — Bestiaire d’Astrub et d’Amakna

La faune des **champs d’Astrub**, de **Tainela**, de la **plage** et du **cimetière** a une fiche jouable : Pious de couleur, Tofu, Boufton Blanc, Bouftou, gelées, Crabe, Pichon, Chafer, Sanglier, Cochon de Lait, Pissenlit Diabolique. Même recette qu’Incarnam : **PA et noms de sorts Dofus**, un ou deux par créature. Les petits (piou, tofu) font **une action** ; gelées, crabe et pichon en font **deux**. Hostiles, le Chafer est agressif. **Pas de boss**. Distinct des invocations (Tofu, Bouftou) et des cousins d’Incarnam (Tofu Chimérique, Boufton Pâlichon).

## Septembre 2026 — Bestiaire d’Incarnam

Les **monstres des champs d’Incarnam** ont une fiche jouable : Piou, Tofu Chimérique, Bouftons, Larves, Arakne, Champ Champ, Moskito, plantes. On a gardé les **PA et les noms de sorts Dofus**, en n’en laissant **qu’un ou deux** par créature. Les petits (tofu, piou, moustique) font **une action** ; les plus complets (larve orange, champignon) en font **deux**. Hostiles, parfois agressifs. Traits déjà au catalogue (petite taille, vif, agile). **Pas de boss** dans cette vague.

## Septembre 2026 — Élément des sorts = celui des effets

L’élément affiché sur une fiche de sort (Air, Feu…) est celui de ses **effets**. Bec du Tofu est **Air**, comme son attaque. Une commande recale les sorts déjà en base.

## Septembre 2026 — PA légendaires des boss

Un monstre **boss** a un pool de **PA légendaires**, distinct de ses PA de tour. Il les dépense **entre** les tours des autres (sorts ou capacités, autant que le restant), **en plus** de son tour. Le pool se recharge à la **fin du tour du boss** — ce n’est pas une réaction. C’est dans L’Essentiel (Combat) et dans le livre. Sur les fiches, le nombre est collé juste après le tag Boss ; on le règle dans l’édition une fois la case Boss cochée.

## Septembre 2026 — Menu Classes : une page par classe

Dans **Bibliothèques**, **Classes** se déplie à nouveau : le tableau reste la page parente, et chaque classe a sa propre entrée une fois le menu ouvert. Les classes encore en brouillon n’apparaissent que pour les MJ et au-dessus ; un visiteur ne voit que celles déjà jouables. Même logique pour **Spécialisations**.

## Septembre 2026 — Invocations de classe

Les sorts d’invocation ont maintenant une **fiche monstre** : Tofu, Dragoune, Poupée, Harponneuse, Gardienne, puis les paliers 7–14 (Bouftou, Bloqueuse, Tactirrelle, Double, Synchro, Malle…). Fragile (peu de PV, 1 PM), une attaque ou un soin. Le sort de classe pointe vers la fiche. Le Coffre de l’Enutrof reste une zone, pas une créature. C’est aussi le modèle à copier plus tard pour les monstres générés par l’IA.

## Septembre 2026 — Passifs des 19 classes

Chaque classe a son **passif** dès le niveau 1 (gratuit) et un **moteur** parmi cinq : zones, invocations, état perso, marques, tempo. Le moteur reste simple au 1 et s’ouvre au 7. Fureur et Contraste donnent +1, pas un dé empilé sur un sort d’identité. Les sorts qui simulaient le moteur avec un +Xd4 (masques, portails, rage, coffre, balise, runes, roulette…) ont été recadrés.

## Septembre 2026 — Les 24 sorts de chaque classe

Les **19 classes** ont leurs **24 sorts** (12 emplacements × 2 variantes) : le kit de niveau 1, puis 9 emplacements aux niveaux 3, 4, 5, 7, 8, 10, 11, 13 et 14. Noms Dofus, chiffres JDR (1d6 → 4d6 selon le palier). C’est un **dégrossissage** à relire : pièges, glyphes et invocations restent simples, pas les combos du MMO. Le seeder rejoue le tout.

## Septembre 2026 — Atelier Création : un guide par entité

L’atelier **Pour les MJ → Création** n’aligne plus trois chartes génériques. Chaque type des Bibliothèques a sa page : **philosophie** (à quoi sert la fiche), **marche à suivre** (comment la créer, bons chiffres, pièges), puis le tableau utile. Les capacités, panoplies, ressources, états et traits n’ont pas de grille dédiée. Les anciennes adresses `contribution-creatures` redirigent. Contribution publique reste **Nous rejoindre**.

## Septembre 2026 — Marin et Courtisan en brouillon

Deux spécialisations de plus, toujours en **brouillon** : **Marin·e** (mer, navires, Sufokia) et **Courtisan·e** (cours, étiquette, intrigue). Comme Artisan, Négociant et Sylvain, ce n’est pas jouable : des pistes palier par palier, pas encore de chiffres ni de capacités liées. On les trouve dans le catalogue (filtre Brouillon), pas dans le menu public.

## Septembre 2026 — Trois spécialisations en brouillon : Artisan, Négociant, Sylvain

Le livre parlait déjà de neuf spécialisations, mais la bibliothèque n’en avait que six. **Artisan·e**, **Négociant·e** et **Sylvain·e** existent maintenant en **brouillon** : une fiche, une présentation, des pistes d’aptitudes et de capacités palier par palier. Ce n’est pas jouable. Les chiffres, les coûts et les vrais liens vers les capacités restent à écrire. Le menu Bibliothèques n’affiche que les spécialisations déjà jouables ; les brouillons se voient dans le catalogue (filtre Brouillon) si tu as le droit d’éditer.

## Septembre 2026 — PNJ : plus de fuites nested, création sans stats Faker

Un PNJ publié n’embarque plus les traits, la boutique, les panoplies, scénarios, campagnes, ni une classe / spé encore en brouillon. Un PNJ brouillon ne fuit plus via le catalogue des boutiques. Créer un PNJ ou un monstre pose une fiche vide (totaux `null`), plus de stats Faker.

## Septembre 2026 — Sept classes ajoutées, fiches et sorts niveau 1

**Roublard, Zobal, Steamer, Eliotrope, Huppermage, Ouginak, Forgelance** ont une fiche (voix du tableau des rôles) et **6 sorts** de niveau 1, même budget que l’Iop. Pas de combo bombe / masque / tourelle évoluée / portail / rune / rage au niveau 1 : on frappe, on se place, on invoque une tourelle à 1d4. **Foggernaut** n’est pas une 20ᵉ classe : c’est le nom anglais du Steamer.

## Septembre 2026 — Voix des 12 classes originales

Les **12 classes de base** ont maintenant leurs trois voix (Terre / Air / Feu / Eau) calées sur le tableau des rôles : Féca protection-tank-amélioration, Iop et Crâ et Ecaflip tout dégât, Eniripsa tout soin, Osamodas invocation, etc. Le seeder les rejoue, le scrapping ne les écrase plus. Sacrieur et Pandawa étaient déjà là ; les dix autres (Féca à Sadida) suivent le même modèle.

## Septembre 2026 — Fiches Sacrieur et Pandawa

Les classes **Sacrieur** (berserker, Terre et Air tank, Eau protection) et **Pandawa** (bagarreur assoiffé, Terre tank, Air placement, Feu dégât) existent en base, comme les autres. Leurs six sorts de niveau 1 se collent maintenant sur la fiche. Pas d’image tant qu’on n’a pas scrapé : c’est cosmétique.

## Septembre 2026 — Six sorts de Sadida, Sacrieur, Pandawa et Ecaflip pour le niveau 1

Le **Sadida** plante : Ronce (Terre) ou Larme (Eau), Tremblement ou Vent Empoisonné, Poupée ou Ronce Apaisante. Le **Sacrieur** encaisse : Punition ou Absorption (vol 1d4), Folie Sanguinaire ou Châtiment, Attirance ou Sacrifice. Le **Pandawa** frappe sans attendre d’être Saoul : Poing Enflammé ou Vague à Lame, Pandatak ou Flasque Explosive, Picole ou Chamrak. L’**Ecaflip** tape sans cartes : Topkaj ou Yams, Pelotage ou Kraps, Bond du Félin ou Entrechat. Même budget que l’Iop.

## Septembre 2026 — Six sorts de Féca, Osamodas et Enutrof pour le niveau 1

Le **Féca** garde : Attaque Naturelle (Feu) ou Rempart (Terre), Glyphe Enflammé ou Escapade, Bouclier Féca (sur soi) ou Armure Aqueuse (sur un allié). L’**Osamodas** frappe puis invoque : Serres du Vautour (Air) ou Griffes du Chtigre (Feu), Déplumage ou Frappe du Craqueleur, Tofu ou Dragoune (1 invocation à 1d4). L’**Enutrof** jette et entrave : Lancer de Pièces (Eau) ou Roulage de Pelle (Feu), Pelle Aurifère (−1 PM) ou Lancer de Pelle (poussée), Maladresse ou Souterrain. Même budget que l’Iop.

## Septembre 2026 — Six sorts d’Eniripsa, Sram et Xélor pour le niveau 1

L’**Eniripsa** soigne : Mot Vivifiant (Eau) ou Mot d’Amitié (Air) à 1d4+Sagesse, Mot Interdit (soin fort) ou Mot Tapageur (zone), Mot de Frayeur (poussée) ou Mot d’Envol (échange avec un allié). Le **Sram** frappe de près : Sournoiserie (Terre) ou Cruauté (Eau), Piège Sournois ou Invisibilité (le sort fort), Dérobade ou Peur. Le **Xélor** pique et contrôle le temps : Aiguille (Feu) ou Gelure (Eau), Raulebaque (dégâts + retour à la case de début de tour) ou Flou Temporel (−1 PA en zone), Téléportation ou Permutation. Même budget que l’Iop et le Crâ.

## Septembre 2026 — Six sorts de Crâ pour le niveau 1

Le Crâ commence avec **3 sorts parmi 6** : Flèche Cinglante (Air) ou Flèche Glacée (Eau), Flèche Explosive (zone Feu) ou Tir Perforant (ligne Air), Œil de Lynx (+2 PO) ou Tir de Recul (poussée, sans dégâts). Même budget que l’Iop. Les flèches de base ont une portée minimale de 2 : collé, tu recules l’ennemi ou tu prends tes distances. Œil de Lynx + une flèche tient dans un tour de 6 PA.

## Septembre 2026 — Six sorts d’Iop pour le niveau 1

L’Iop commence avec **3 sorts parmi 6** : Pression (Terre) ou Attaque Naturelle (Feu), Fendoir (zone) ou Intimidation (poussée), Bond ou Concentration. Même budget que les autres classes (3 PA pour l’attaque de base, 1d6+mod). Bond + Pression tient dans un tour de 6 PA. Les autres sorts scrapés de la classe passent hors grille, en attendant leur palier.

## Septembre 2026 — Rappel, antidote, bière, café et renaissance

Une **Potion de Rappel** à 800 kamas (le billet de zaap chez un PNJ reste à 80). Un **Antidote** à 1 500 kamas retire Empoisonné. La **Bière d’Amakna** (+1 Supercherie) et le **Café** (+1 Investigation) tiennent jusqu’au prochain repos long. L’**élixir de Wakfu** redonne 2 points de réserve pour 3 500 kamas. Le **Bonbon de Renaissance du Chanceux** (niveau 12, 10 000 kamas) te laisse à 1 PV au lieu de tomber à 0. Deux potions de bouclier et deux réserves de PV temporaires complètent le lot. Même type d’effet : pas de cumul. Les parchemins de caractéristique montent aussi en niveau et en rareté (petit niv. 3, puissant niv. 15 Très rare).

## Septembre 2026 — Parchemins pour rééquilibrer les caractéristiques

Les parchemins Dofus qui **ajoutaient** de la Force, de la Chance, etc. ne collent pas au JDR (budget de points fermé). Ils **libèrent** maintenant des points déjà mis dans **une** caractéristique pour les replacer ailleurs : petit = 1 point (1 000 kamas), normal = 2 (3 000), grand = 3 (5 000), puissant = 4 (10 000). Six caractéristiques, donc vingt-quatre fiches. Pas de recette : on les achète. Hors combat, usage unique, on ne descend pas sous le plancher de création (8, ou 6 si c’était la faiblesse choisie).

## Septembre 2026 — Pain, poisson, viande et potions de soin

Une **dizaine de paliers** (onze) pour chaque type : +1, +3, +5, +7, +10, +12, +15, +17, +20, +25 et +30 PV, **hors combat uniquement**. Les prix vont de 20 à 10 000 kamas. Chaque recette prend **10** unités de la ressource du palier, vendue au dixième du prix du consommable. Les noms et images Dofus sont gardés quand la fiche existait (Pain d’Incarnam, Goujon en tranche, Potion de Mini Soin…) ; les plus gros flacons n’avaient pas d’équivalent, on les a créés.

## Septembre 2026 — Plus de ressource jouable à 0 kama

Une ressource **jouable** vaut au moins **1 kama** : on ne laisse plus le blé ou un minerai afficher 0. Les ingrédients de l’échelle de soin ont des prix plus hauts, calés sur le consommable.

## Septembre 2026 — Filtre état sur les équipements

Le catalogue d’équipements a les mêmes pastilles d’état que les autres listes (Brut, Brouillon, Auto, Jouable, Archivé), à droite de « Filtres ». Au chargement, **Jouable** est coché.

## Septembre 2026 — Carte déployée au-dessus des voisines

Survoler une petite carte (panoplie, équipement…) l’ouvre **par-dessus** celles d’en dessous, plus en dessous.

## Septembre 2026 — Catalogues sans types précochés, état « Jouable »

Les catalogues (équipements, panoplies, consommables, ressources, monstres, sorts) arrivent avec le filtre **Type** (ou Race) sur **Tous** : plus de liste de types cochée d’avance. Survoler le point d’état d’une fiche (carte minimale ou fiche complète) dit **Jouable**, plus « Actif ».

## Septembre 2026 — Ressources des recettes d’équipement jouables

Les ingrédients des équipements relus (panoplies et pièces jusqu’au niveau 8) sont maintenant **jouables** : nom et type Dofus inchangés. Un prix à 0 côté Dofus passe à **1 kama** une fois la fiche jouable. Le set Intrépide n’a toujours pas de recette. On n’a pas publié tout l’arbre de métier Dofus collé derrière chaque ressource.

## Septembre 2026 — Une compétence légère sur (presque) chaque set

Les compétences (+1 Athlétisme, Arcanes, Discrétion…) pèsent peu par rapport à une caractéristique : on s’en sert comme **bonus facile** sur les panoplies relues, y compris plus haut niveau. Si le chapeau ou la cape est vide, ça va sur la pièce ; sinon sur le palier complet du set. Les Pious Rose (vitalité) et Violet (portée) n’en ont toujours pas.

## Septembre 2026 — Pious sans carac primaire, total set + pièces

Les six Pious du niveau 1 ne donnent plus Force / Intelligence / Chance / Agilité au set complet. Vert, Rouge, Bleu et Jaune portent la **compétence** de l’élément (Athlétisme, Arcanes, Supercherie, Acrobaties) plus la portée. Le Rose, côté vitalité, n’a **pas** de compétence : seulement des points de vie max (l’anneau garde le +1 soin). Le Violet reste +1 PO.

Sur la petite carte, une fois déployée, on voit sous les paliers du set la **somme** de tout ce que donnent les équipements **et** le bonus de panoplie, chips à la suite, sans le chiffre de pièces devant.

## Septembre 2026 — Bonus de panoplie lisibles, carte par carte

Les bonus d’un set ne sont plus collés en une seule ligne : **une ligne par nombre de pièces** (badge + bonus), et sur la fiche complète **une colonne par palier** (chiffre en haut, caractéristiques en dessous avec icône et nom). Survoler une carte du catalogue ne déploie plus toutes les autres.

## Septembre 2026 — Exemples de panoplies pour l’IA

Le Bouftou complet donne **force et intelligence** (comme Dofus), plus le +1 PA : les deux caracs ne sont pas sur les pièces, seulement quand tout le set est porté.

Une cinquantaine de sets connus sont maintenant jouables (Blop, Gelax, Craqueleur, Mulou, Koalak, Kitsou, Wabbit, Cawotte, Tortue, Chef Crocodaille, Aerdala, Terrdala, Invisible, Champêtre, Homme Ours, etc.). C’est la liste d’exemples que l’IA doit imiter, pas tout le catalogue scrapé.

## Septembre 2026 — Filtrer les équipements et panoplies par bonus

Sur les catalogues d’équipements et de panoplies, on choisit **une caractéristique** (force, vitalité, PA…) dans une liste, et seulement alors le filtre min/max s’affiche — comme sur DofusDB. Les autres filtres avancés (PA d’un sort, stats d’un monstre, etc.) suivent la même idée : « Ajouter un filtre » plutôt que d’étaler tous les curseurs. Pour une panoplie, le chiffre filtré est la **somme** des bonus de tous les paliers du set.

## Septembre 2026 — Voie sur le set complet, pas sur chaque pièce

Aux tout premiers niveaux, on ne peut pas coller Force / Intelligence / Chance / Agilité sur une cape sans dépasser le plafond. Ces bonus vont donc sur **toute la panoplie équipée**. Le Bouftou complet reprend Dofus : +1 force **et** +1 intelligence (plus le PA). Beaucoup de pièces restent vides, et ce n’est pas un problème.

Quelques chapeaux ou capes (puis, plus tard, presque tous les sets) donnent +1 à une compétence : ça pèse moins qu’une caractéristique, donc on s’autorise un écart avec la grille stricte. On ne décline pas ces sets en plusieurs raretés : il n’y a pas assez de marge de bonus.

## Septembre 2026 — Niveau des panoplies

Une panoplie n’a pas de niveau à elle : on affiche celui de **la pièce la plus élevée**. Sur le catalogue, un curseur min/max filtre comme pour les équipements, et la liste s’ordonne du plus petit niveau au plus grand. Le badge apparaît sur les cartes (repliées et déployées), en liste, sur la fiche, et en lecture seule à l’édition.

## Septembre 2026 — L’écran de chargement ne reste plus bloqué

Au lancement, l’app s’arrêtait sur « Chargement… » : un découpage trop agressif du JavaScript (layout d’un côté, formatters de l’autre) empêchait les scripts de s’initialiser. Le build ne sépare plus que les bibliothèques tierces. Le favicon pointe vers les fichiers logo qui existent vraiment (webp / png / ico), plus vers un SVG manquant qui déclenchait un accès refusé.

## Septembre 2026 — Compilation du livre depuis l’admin

Le bouton **Compiler le livre de règles** (Gestion du contenu) avait perdu sa route lors de l’ajout du recalcul des prix. Elle est rétablie, et le bouton lance la génération même si aucun worker n’était déjà en route.

## Septembre 2026 — Prix automatiques en kamas

Les fiches d’équipement et de consommable portent maintenant un **prix calculé** par le site, que l’on peut encore ajuster à la main.

Un objet vaut la somme de ses bonus (chaque caractéristique a un prix unitaire dans le tableau MJ) plus 150 kamas par niveau et 200 kamas par palier de rareté. Un consommable vaut la somme des ressources de sa recette. Les ressources gardent le prix proposé par Dofus.

À la création, à l’import, ou via « actualiser » sur la fiche (et en masse depuis **Gestion du contenu**), le calcul remplace le total affiché. Un ajustement manuel reste possible ensuite.

## Septembre 2026 — Client Inertia Vue 3

Le pont entre Laravel et les pages Vue passe par Inertia 3 côté navigateur. Navigation, formulaires, titres de page et déconnexion restent les mêmes gestes ; le client HTTP interne d’Inertia gère les visites (axios reste pour les appels API métier).

## Septembre 2026 — Grille d’équipements JDR

On ne garde plus l’idée d’importer tout Dofus pour habiller un perso. Une commande (`ia:equipment-grid`) dresse la carte **niveau × emplacement × voie** (Terre, Feu, Eau, Air). Quand un objet Dofus colle à une case, on le note comme représentant **sans toucher à son nom ni à son icône**. Les cases vides peuvent être remplies par des fiches techniques en brouillon, à relire avant de les rendre jouables.

## Septembre 2026 — Premiers équipements jouables, classés par rareté

32 objets de **niveau 8** sont passés en jouable : 4 capes et 4 armes pour chacun des quatre éléments, chaque série étant déclinée de Commun à Très rare. À élément et niveau identiques, on peut donc comparer quatre objets qui donnent de plus en plus de bonus — c’est la rareté qui fait la différence.

Ce qu’il faut savoir en tant que MJ : seules les **capes** portent Force, Intelligence, Chance ou Agilité, et seules les **armes** portent des dégâts élémentaires. Les anneaux, amulettes, chapeaux, ceintures et bottes ont leurs propres bonus (PA, portée, Vitalité, tacle…) et ne sont pas concernés par les éléments. Les objets de ces emplacements repassent donc en brouillon pour l’instant.

Les valeurs suivent les grilles de normes du jeu et ne dépassent jamais le plafond du niveau. Les noms, descriptions et illustrations Dofus n’ont pas bougé.

## Septembre 2026 — Sauvegarder et rejouer les objets relus

Les objets validés à la main ne vivent plus uniquement en base : chacun a désormais son fichier dans le dépôt, lisible et modifiable à la main. On peut donc reconstruire le socle d’objets jouables sur une base neuve, ce qui est indispensable pour faire tourner l’IA sans repartir de zéro.

L’aller-retour base ↔ fichiers se fait en CLI (`items:seeder-export` / `items:seeder-import`) ; hors production, `project:backup` (page **Sauvegarde**) réécrit aussi ces JSON depuis la base. Plus de boutons d’export/import seeders dans l’UI admin (risque de compromission des données versionnées).

Une réinstallation complète du projet recrée maintenant ces objets automatiquement. Les visuels ne sont pas concernés : ils restent gérés par la médiathèque.

## Septembre 2026 — Panoplies connues d’Astrub / Amakna

Après les Pious / Bouftou / Tofu / Prespic, les sets qu’on croise ensuite sont aussi **jouables** (filtre d’état sur le catalogue) : Sanglier, Arakne, Moskito, Champ Champ, Bandit, Jeune Aventurier, Paysan, Larvesque, Bouftou Royal, Abraknyde, les quatre Kwak, les quatre Scarafeuilles et Akwadala.

Même logique qu’avant : chaque pièce garde son nom Dofus ; les bonus JDR restent calés sur l’emplacement (cape = élément à partir du niveau 3, arme = dégâts, ceinture = tacle ou fuite, bouclier = CA). Le Bouftou Royal reprend le +1 PA du Bouftou une fois le set complet ; les Kwak et Akwadala donnent de la portée au complet.

## Septembre 2026 — Panoplies des premiers niveaux

Les sets qu’on croise dès Incarnam / Astrub sont jouables : les **six Pious** (niveau 1, une couleur par élément, plus rose pour les soins et violet pour la portée), le **Bouftou**, le **Tofu** et le **Prespic**. Chaque pièce garde son nom et son illustration Dofus.

Les bonus JDR suivent l’emplacement (une ceinture tacle ou fuit, un marteau tape Terre, un bouclier donne de la CA). Aux tout premiers niveaux, Force / Intelligence / Chance / Agilité ne peuvent pas encore aller sur une cape : c’est le **bonus de set** qui porte la voie (et le +1 PA du Bouftou complet, le +1 PM du Tofu complet).

## Septembre 2026 — Tableaux : TanStack Table 9

Les catalogues (tri, pagination, colonnes visibles) passent en TanStack Table 9. L’usage ne change pas : on trie, on pagine, on masque des colonnes comme avant.

## Septembre 2026 — Tests PHP : PHPUnit 13

Les tests backend tournent avec PHPUnit 13. On lance toujours `php artisan test`. Un mock qui filtrait `with()` sans `expects()` a été corrigé (PHPUnit 13 le déconseille).

## Septembre 2026 — Miniatures d’images : Intervention 4

Les miniatures (WebP, JPEG, etc.) passent par Intervention Image 4. Le recadrage cover/contain ne change pas ; l’enregistrement utilise le nouvel encodeur de format.

## Septembre 2026 — État Pinia 4

Les petits stores (caractéristiques, panneau DofusDB, modal favoris) passent en Pinia 4. L’API ne change pas (`defineStore`, `storeToRefs`). Pinia 4 exige le paquet `@vue/devtools-api` à côté.

## Septembre 2026 — Lint du front : ESLint 10

Le contrôle de qualité du JavaScript et des composants Vue passe en ESLint 10. On lance toujours `pnpm lint` ; la config plate ne change pas. Il faut Node 20.19+, 22.13+ ou 24+ (déjà couvert par Node 22+ du projet).

## Septembre 2026 — concurrently 10 (Node 22+)

Les scripts qui lancent Vite et le CSS en même temps (`dev:css`, `composer run dev:network`) utilisent concurrently 10. Ça impose **Node 22 ou plus** (plus de Node 20). Les options `--kill-others` et les couleurs de préfixe n’ont pas changé pour nous.

## Septembre 2026 — Markdown légal : marked 18

Les pages CMS qui affichent un markdown (mentions légales, etc.) passent par marked 18. L’appel `marked.parse` reste le même ; le HTML a juste moins de lignes vides en trop.

## Septembre 2026 — Couleurs oklch : plugin PostCSS 5

Le plugin qui traduit `oklch()` en RGB de repli (tooltips, ombres) passe en version 5. La config PostCSS (`preserve: true`) ne change pas.

## Septembre 2026 — Tooltips et menus : Floating UI Vue 2

Les infobulles et menus flottants utilisent `@floating-ui/vue` 2 (Vue 3.5, plus de `vue-demi`). L’API `useFloating` ne change pas.

## Septembre 2026 — Tests front : jsdom 30

L’environnement Vitest passe de jsdom 26 à 30. Aucun test à réécrire : la suite Vitest passe. jsdom 30 demande Node 22.22.2+, 24.15+ ou 26+.

## Septembre 2026 — CI GitHub : checkout v7 et composer-install v4

Les workflows de PR clonent avec `actions/checkout@v7` et installent PHP avec `ramsey/composer-install@v4`. Ça reste déclenché par `pull_request` (pas de `pull_request_target`) : pas besoin du flag « checkout non sûr ». Un runner auto-hébergé doit être en 2.327.1 ou plus.

## Septembre 2026 — Changer l’état depuis une carte minimale

Sur une petite fiche (catalogue, grille), le menu d’état et le menu « ⋮ » s’ouvraient hors de la carte. Dès qu’on quittait la carte pour cliquer une option, la fiche se repliait et le menu disparaissait : impossible de changer l’état. La carte reste maintenant ouverte tant que le menu est là, pour toutes les entités.

## Septembre 2026 — Correctif `qs` (Inertia)

La librairie `qs` tirée par Inertia passait encore en 6.15.3 dans le lockfile : GitHub signalait deux failles modérées (parsing d’objets trop profond, DoS). Elle est forcée en 6.16.0, sans changer Inertia ni le code du site.

## Septembre 2026 — Index atomic design : glob 13

Le script qui liste les Atoms / Molecules / Organismes utilise `glob` 13 au lieu de 11 (version abandonnée). L’index a été régénéré : les templates CMS forgemagie et téléchargements y figurent maintenant.

## Septembre 2026 — Fiches brouillon : plus de fuites via l’API et les liaisons

Un invité ne lit plus les effets d’une fiche brouillon (`/api/effects/for-entity`, `/api/effects/usages`, `/api/object-effects`). Sur une fiche jouable, les liaisons encore en brouillon disparaissent : ingrédients de recette, objets/campagnes liés à une ressource, spécialisations d’une capacité, traits d’un monstre **ou d’un PNJ**, bonus d’équipement dans `resolved-stats`, sorts d’une page bibliothèque CMS. Boutique, panoplies, scénarios et campagnes d’un PNJ jouable aussi ; un PNJ brouillon ne fuit plus via le catalogue des boutiques. Une maj DofusDB forcée ne dépublie plus une panoplie jouable.

## Septembre 2026 — Registres de types : API réservée aux admins

Les pages Types étaient déjà interdites aux MJ, mais l’API `/api/types/*` (races, types de sorts) restait ouverte à tout compte connecté. Un MJ pouvait donc cocher « scrap » ou supprimer une race créée automatiquement. Ces actions sont maintenant admin uniquement, comme le reste de la gestion du contenu.

## Septembre 2026 — Maj DofusDB « images seules » sans écraser la fiche

Le bouton « Images seules » d’une fiche ne réécrit plus le contenu (sorts, bonus d’objet, pièces de panoplie). Seule l’image est mise à jour.

## Septembre 2026 — Les PNJ ont une vraie fiche de jeu

Un personnage non-joueur n’est plus une coquille vide collée à une créature. On le crée depuis le catalogue (nom, lieu, classe, rôle), on lui donne une taille comme aux monstres, des langues, une histoire. Le catalogue se feuille comme celui des monstres : page par page, on filtre l’hostilité ou les stats, on trie par nom. Il est dans **Bibliothèques**, juste après les monstres. Sur la fiche et la petite carte : sorts, compétences, équipement porté (un objet par emplacement, deux anneaux), panoplies associées et boutique s’il en a une. Pas d’import DofusDB : ces gens-là s’écrivent à la main.

## Septembre 2026 — Pied de page plus bas, sur deux lignes

Le bandeau en bas d’écran ne s’étale plus en un bloc haut. Deux lignes : le nom et la version à gauche, le logo au milieu, Contact / Discord / GitHub à droite ; en dessous le texte du projet, et le bouton Cookies tout à droite. Sur téléphone, le dock en bas ne change pas.

## Septembre 2026 — Un dé pour toucher, l’esquive PA/PM en second seuil

En combat, plus de troisième « jet d’esquive ». Un sort à touche lance **un** d20 : ça sert pour les dégâts (contre la CA) **et** pour un retrait de PA ou de PM (contre l’esquive). Un sort à sauvegarde se lance toujours ; si ton esquive PA/PM est au moins égale au DD du sort, tu évites le retrait même quand le reste te tombe dessus. Pas de moitié de dégâts automatique : c’est écrit sur la fiche, ou pas.

## Septembre 2026 — Livre de règles en PDF et ODT

Le livre se télécharge, plus seulement se lit en ligne. Un administrateur lance la compilation (bouton dans la gestion du contenu, ou après un import des règles) : ça produit un PDF et un OpenDocument, stockés une fois pour toutes. Les joueurs les récupèrent sur **Règles → Ressources et équilibrage → Ressources**, avec la fiche de personnage et le logo. Ajouter un fichier plus tard, c’est une ligne dans le catalogue.

## Septembre 2026 — Les métiers : 6 maximum, 5 niveaux, une page dédiée

Un personnage apprend **6 métiers au maximum**, toutes catégories confondues : rien n’oblige à répartir entre récolte, artisanat et forgemagie. Les anciens paliers de niveau 1 à 50 avec titres et bonus « -10 % de ressources » ont disparu.

Chaque métier a désormais **5 niveaux**. Pour l’artisanat et la forgemagie, le niveau dit jusqu’à quelle **rareté** on peut monter : 1 Commun, 2 Peu commun, 3 Rare, 4 Très rare, 5 Légendaire. Pour la récolte, il dit quelle **bande de ressources** on peut atteindre : 1 à 4, 5 à 8, 9 à 12, 13 à 16, 17 à 20. Fabriquer ou forgemager demande les deux conditions : le niveau de métier couvre la rareté, et le personnage a au moins le niveau de l’objet. Les objets de rareté **Unique** ne se fabriquent plus : ce sont des pièces de quête ou du MJ.

La liste est fixée à **16 métiers** : 9 d’artisanat (Bijoutier, Cordonnier, Tailleur, Sculpteur, Forgeron, Bricoleur, Boulanger, Boucher, Poissonnier), 6 de récolte (Paysan, Bûcheron, Mineur, Chasseur, Pêcheur, Alchimiste) et la forgemagie, qui devient **un seul métier** au lieu d’une spécialisation par rune. « Sculpteur d’armes », « Forgeur d’armes » et « Forgeur de boucliers » fusionnent en Sculpteur et Forgeron ; « Herboriste » n’est plus qu’un autre nom de l’Alchimiste.

Nouvelle page **Bibliothèques → Les métiers** : le système résumé, les 16 métiers illustrés avec leur caractéristique, et le **tableau des prix de runes**. Ce tableau se lit directement dans la base — plus aucun prix recopié dans le livre. Il affiche donc les vrais chiffres du jeu (une rune coûte le double du prix de la caractéristique sur équipement, soit 2 600 kamas pour un PA et non 3 900) ainsi que les emplacements réellement autorisés pour chaque rune.

## Septembre 2026 — Désenvoûtable devient dissipable

Le livre et l’interface disaient déjà « dissipable ». Les textes scrapés (sorts) et deux sections CMS gardaient le mot Dofus « désenvoûtable ». Ils disent maintenant « dissipable » ; un prochain import DofusDB le réécrit tout seul. Les noms du type *Désenvoûtement* ne bougent pas.

## Septembre 2026 — Filtres panoplie, dissipable, bordure d’élément

Le catalogue des panoplies filtre le nombre de pièces et les types d’objets du set ; les pastilles d’état listent tous les états (plus seulement Brut). Sur les états, « Dissipable » est un interrupteur. Les cartes sorts et capacités ne teintent plus tout le fond : une bordure reprend la couleur d’élément, en dégradé s’il y en a plusieurs (Terre comprise).

## Septembre 2026 — Filtres de catalogue unifiés

Les tableaux d’entités filtrent le **niveau** avec un curseur min/max (toute la bibliothèque, plus seulement la page affichée). L’**état** (Jouable, Brouillon…) se coche en pastilles à point à droite du titre « Filtres », Jouable par défaut. Types et listes longues restent un menu compact. PA, PO, PM (et les interrupteurs liés) sont dans les filtres avancés, avec l’icône et la couleur de chaque caractéristique. Les champs texte des filtres ont disparu : on cherche ça dans la barre de recherche. Sur les monstres, l’état de la fiche et le niveau de la créature suivent la même règle.

## Septembre 2026 — L’Essentiel = aide-mémoire

Le menu L’Essentiel reprend les canons du livre (repos, maîtrise, DD, potions…) en pages courtes : un chapitre par sujet, pour retrouver une règle sans relire le tome. Source : `database/seeders/data/essential-pages.php`.

## Septembre 2026 — Livre de règles aligné, notes sur les fiches

Les chiffres du livre parlent d’une seule voix (round de 6 s, maîtrise, caracs, repos, DD, invocations, réactions, potions). Les chapitres se renvoient au lieu de se recopier. Sur les fiches, des rappels courts : sorts (soin, invocation, piège, glyphe, bouclier…), consommables (cumul, parchemin de sortilège) et montures.

## Septembre 2026 — Cartes sorts / capacités teintées par l’élément

En vue minimale et en ligne, un sort ou une capacité garde le fond glass du thème. L’élément se lit sur la **bordure** (dégradé s’il y en a plusieurs). Sans élément, la bordure reste celle du thème.

## Septembre 2026 — Review : emplacements d’équipement et tests

Les bonus d’objet (acrobaties, toucher, critique…) retrouvent leurs emplacements DofusDB (chapeau, cape, amulette…). Les catalogues alignent les tests sur la colonne Bonus et le pré-coche Type via « visible en jeu ». La recherche de monstres en test n’appelle plus DofusDB à vide : une race scrapable est posée, ou on demande toutes les races.

## Septembre 2026 — Storage public versionné (sauf entités et utilisateurs)

Les fichiers de `storage/app/public` partent sur GitHub (icônes, fonds, logos, légal, fonts…). Restent locaux : les illustrations d’entités (`images/entity`) et les fichiers utilisateur (`images/users`). Le lien `public/storage` se recrée avec `php artisan storage:link`.

## Août 2026 — Filtres de tableau : largeur selon le contenu

Les filtres des catalogues ne s’alignent plus sur trois colonnes identiques. Rareté, état et autres listes courtes s’affichent en pastilles ; type, race et listes longues restent un menu avec recherche. Les interrupteurs et champs texte prennent seulement la place dont ils ont besoin.

## Août 2026 — Tableaux : plus de quick edit, sélection et raccourcis

L’édition groupée dans les catalogues (panneau latéral, toggle Quick edit) a été retirée : on modifie une fiche via la page Modifier. On peut à nouveau cocher des lignes dans les trois vues (minimal, ligne, colonnes) pour l’export CSV ou le PDF. Les cases restent visibles. Les raccourcis du tableau ne volent plus la saisie (recherche, filtres) ni Entrée/Espace sur un bouton ; Ctrl+N (nouvelle fenêtre du navigateur) n’est plus intercepté.

## Août 2026 — API DofusDB, entrée dev et hub commandes

L’atelier parle à `/api/dofusdb` (l’ancienne adresse `/api/scrapping` redirige). `composer run dev` lance la même chose que `project:dev` avec la file. Les fiches isolées de type de ressource ouvrent la page Types commune. Sur le récapitulatif, le super-admin voit la liste des commandes avec un lien vers chaque page déjà en place.

## Août 2026 — URLs contenu sous `/admin/content`

Caractéristiques, effets, langues et mappings DofusDB sont dans l’espace contenu, avec la même racine d’URL que l’atelier. Les anciens liens `/admin/characteristics` etc. redirigent. Les boutons « types » des catalogues ouvrent directement la page Types.

## Août 2026 — Types : en jeu et scrap

Sur la page Types, chaque registre (équipements, ressources, consommables, races, sorts) a les mêmes deux cases : visible en jeu (les tableaux catalogues le pré-cochent) et scrapable (atelier, CLI et maj d’une fiche DofusDB ne touchent que ces types, sauf demande explicite « tout récupérer »). On peut toujours déplacer un type d’équipement, ressource ou consommable vers une autre de ces trois familles.

## Août 2026 — Menus, jobs et types

On passe d’un espace à l’autre par le menu du header, plus par un raccourci dans les barres. Les retours utilisateurs gardent le menu d’administration. Un job (mise à jour stack, sync…) s’annule depuis le toast ou le panneau ; s’il reste coincé à 1 %, c’est souvent que le worker de file n’est pas lancé. Les cinq registres de types (équipements, ressources, consommables, races, sorts) partagent une même page, avec un menu d’actions par ligne.

## Août 2026 — Atelier DofusDB dans le contenu, maj unitaire sur la fiche

L’import DofusDB n’est plus un coin « scrapping » à part. Les **admins** gèrent tout ça dans **Gestion du contenu** : un atelier unique pour chercher, importer, mettre à jour en masse ou ne récupérer que les images, plus les types, les mappings et les référentiels (caractéristiques, effets, langues). Les **MJ** restent sur les fiches : créer, modifier, et lancer une mise à jour DofusDB **pour une fiche à la fois** (contenu ou images). Le **super-admin** s’occupe du serveur (cron, sauvegardes, caches) ; le sync de masse vit dans l’atelier, plus dans l’ops. Les types du catalogue (équipements, ressources, consommables) se cochent désormais dans les registres, plus via une liste figée.

## Août 2026 — Lien admin ↔ contenu et suivi live des jobs

Depuis l’espace administration, un lien mène à la gestion du contenu (et l’inverse pour les admins). Les commandes lancées depuis les pages thématiques (review, nettoyage, stack, sauvegarde, sync) affichent un pourcentage, un toast animé et la sortie console filtrée. Tant qu’un job du même type tourne, on ne peut pas en relancer un second.

## Août 2026 — Pages admin thématiques et planning cron

Les commandes utiles restent chacune sur leur page (sync, backup, review, stack, orphelins, **nettoyage caches**). Le planning cron affiche la commande Artisan réellement lancée et un lien vers la page correspondante.

## Août 2026 — Confirmations CLI `-y` / `--no`

`project:prepare`, `project:dev`, `project:deps` et les autres commandes qui demandent oui/non (IDE Helper, apt, refresh, permissions) acceptent `-y` pour valider et `--no` pour refuser. `-n` reste le mode non interactif de Symfony.

## Août 2026 — Commandes projet simplifiées

Une commande par rôle : `project:dev`, `project:deps`, `project:review`, `project:data sync`, `project:clear`, `project:backup`. Plus d’alias ni de wrappers. La liste tient dans `app/Console/COMMANDS.md`. Le cron admin appelle les mêmes noms.

## Août 2026 — Une seule `main`, branches courtes

Le dépôt n’a plus que `main`. Les fuites de brouillons (panoplies, pièces de set, sorts via monstre / classe / spécialisation), les tooltips catalogue et le travail local (états Auto, table de bonus, conditions canoniques) sont dans `main`. On crée une branche par sujet, on merge, on la supprime. Pas de branches longues `ui` ou `security`.

## Août 2026 — Sorts branchés sur les états de base

Les sorts n’affichent plus chaque état Dofus comme une fiche à part. Quand l’effet correspond à Pesanteur, Empoisonné, Étourdi, Ralenti ou Affaibli, le sort pointe vers cette fiche JDR. Les milliers d’états scrapés restent en Brut, pour le travail d’import.

## Août 2026 — Pages Pour les MJ et atelier Création

Les MJ ont un cinquième bloc de menu, **Pour les MJ**, avec l’atelier **Création**. Dedans : le tableau vivant des bonus d’équipement (par emplacement et par niveau) et les chartes créatures / objets / sorts. La page Contribution, toujours visible de tous, ne sert plus qu’à **Nous rejoindre** (Discord, GitHub, droits).

## Août 2026 — Catalogue des états : Brut masqué, effets visibles

Le catalogue d’états n’affiche plus par défaut les milliers d’états Dofus (ils restent en **Brut**, on peut les recocher dans le filtre). Les quelques états JDR (Pesanteur, Empoisonné, etc.) restent jouables. Sur une fiche, on voit les effets mécaniques (ne pas être déplacé, invulnérable…).

## Août 2026 — État Auto des fiches

Les fiches (sorts, objets, pages, etc.) ont un cinquième état : **Auto**. C’est une proposition à relire (IA ou script), pas encore jouable. Les joueurs ne la voient pas ; les MJ et éditeurs oui, comme un brouillon. Dans les listes, l’ordre est Brut → Brouillon → Auto → Jouable → Archivé.

## Août 2026 — Catalogue ressources : types par défaut

Le filtre Type du catalogue de ressources arrive précoché sur les types métier (bois, minerai, plante, cuir, runes, etc.). Quêtes, souvenirs, zones et essences de donjon restent disponibles dans le filtre, mais seulement si on les coche.

## Août 2026 — Infobulles sur type, rareté et caractéristiques

Survoler le type, la rareté ou le niveau d’une ressource, d’un équipement ou d’un consommable affiche une courte explication (rareté de Commun à Unique, catégorie métier, etc.). Les stats du tableau des caractéristiques ajoutent aussi leurs limites min/max quand elles sont des nombres fixes.

## Août 2026 — Catalogue consommables : types par défaut

Le filtre Type du catalogue de consommables arrive précoché sur potions, nourritures, boissons, parchemins, pierres d’âme, objets utilisables, etc. Certificats, coffres et fées d’artifice restent disponibles dans le filtre, mais seulement si on les coche.

## Août 2026 — Cartes minimales, tooltips et bibliothèques

Les tooltips et popovers d’entités n’affichent plus une seconde boîte autour du contenu (sorts inclus). Les bibliothèques s’ouvrent en vue minimale plutôt qu’en lignes. Sur une carte déployée, la description des consommables, ressources et équipements se lit en entier.

## Août 2026 — Sets brouillon masqués sur un équipement public

Sur une fiche ou un catalogue d’objet visible par tout le monde, on ne voit plus une panoplie encore en brouillon, ni les autres pièces cachées de ce set. Un MJ continue de les voir pour travailler.

## Août 2026 — Panoplie publique : pas les pièces brouillon

Sur une panoplie jouable, le catalogue et la fiche de lecture ne listent que les équipements que le visiteur a le droit de voir. Un objet encore en brouillon, même accroché au set, n’apparaît plus pour un joueur. La page Modifier continue d’afficher toutes les pièces liées, pour ne pas les retirer par erreur.

## Août 2026 — Sorts brouillon masqués sur un monstre jouable

Sur une fiche monstre (catalogue ou page), un joueur ne voit plus les sorts encore en brouillon. Seuls les sorts qu’il a le droit de consulter apparaissent, comme pour les équipements.

## Août 2026 — Sorts brouillon masqués sur les classes

Un sort (ou une capacité) encore en brouillon n’apparaît plus sur la fiche, le catalogue ou le PDF d’une classe jouable. Il reste visible pour l’auteur, les MJ et les admins, et sur la page Modifier.

## Août 2026 — Sorts brouillon masqués sur les spécialisations

Un sort (ou une capacité) encore en brouillon n’apparaît plus sur la fiche, le catalogue ou le PDF d’une spécialisation jouable. Il reste visible pour l’auteur, les MJ et les admins, et sur la page Modifier.

## Août 2026 — Équipements du set dans le tooltip panoplie

Le survol de l’icône de panoplie sur une fiche équipement liste les pièces en vue texte : un clic sur un nom ouvre la fiche de l’objet, comme sur la page de la panoplie.

## Août 2026 — Tooltip sur une carte minimale

Survoler le tooltip d’une fiche minimale (grille) ne replie plus la carte : elle reste ouverte le temps d’atteindre et de lire l’infobulle.

## Août 2026 — Tooltip : on peut le survoler

Les infobulles ne se ferment plus dès que la souris quitte le déclencheur : tant que le pointeur est sur le tooltip, il reste affiché, partout sur le site.

## Août 2026 — Panoplie visible sur l’équipement

Si un objet fait partie d’une panoplie, les fiches équipement montrent l’icône du set. En carte repliée, l’icône seule ouvre un aperçu (nom, autres pièces, bonus). En carte déployée et en liste, le nom apparaît à côté. La fiche complète liste les pièces et les bonus par palier (boutons chiffrés, comme les sorts).

## Août 2026 — Modifier une panoplie : ordre et style

Sur la page Modifier, on commence par les équipements et les bonus de set. Nom, description et droits (lecture / écriture) sont en bas. Les bonus utilisent les mêmes champs verre que le reste du site, plus de panneau bleu plein.

## Août 2026 — Éditer ouvre la page Modifier

Le crayon du menu d’options (carte, ligne, tableau) ouvre la même page Modifier que le bouton Éditer de la fiche. L’ancienne fenêtre « Édition rapide » d’une seule entité a été retirée : elle n’avait plus les mêmes champs que la page. On peut toujours modifier plusieurs lignes d’un coup via le panneau du tableau.

## Août 2026 — Modifier une panoplie : pièces et bonus

On peut chercher un équipement à ajouter (catalogue, pas une liste figée), le retirer de la panoplie, et saisir les bonus de set comme les effets d’un objet : une caractéristique et une valeur, par nombre de pièces.

## Août 2026 — Afficher ouvre la fiche en modal

Sur toutes les entités, Afficher (carte minimale, ligne, liste) ouvre le détail en fenêtre, pas la page. On passe à la page complète depuis cette fenêtre (Agrandir) ou avec Ctrl+clic.

## Août 2026 — Afficher une panoplie ouvre la fiche

Le bouton Afficher d’une panoplie dans la liste ouvre la modal de détail, au lieu de recharger le tableau. Agrandir (ou Ctrl+clic) mène à une vraie page de lecture.

## Août 2026 — Fiches overlay : une seule carte

Ouvrir une entité depuis un nom (équipement d’une panoplie, sort d’un monstre, etc.) n’affiche plus une grande boîte vide autour de la fiche. La carte s’ajuste à son contenu. Le menu « ⋮ » n’a plus de fond gris derrière les icônes.

## Août 2026 — Vues panoplie : pièces et vignette

Les fiches panoplie (ligne, carte, détail) listent les équipements en vue texte (icône + nom, aperçu au clic). La vignette reprend les images des pièces s’il y en a, sinon le nom en initiales. Les bonus de set ne s’affichent plus en `[object Object]`.

## Août 2026 — Saisie dans les filtres des tableaux

On peut à nouveau taper dans les champs des filtres (recherche d’un type, filtres texte, etc.) : la valeur n’est plus effacée à chaque rafraîchissement de la liste.

## Août 2026 — Catalogue objets : types de jeu par défaut

Le filtre Type du catalogue d’équipements arrive précoché sur les emplacements utiles en jeu (amulette, armes, cape, dofus, familiers, trophées, etc.). Apparats, costumes et autres cosmétiques restent disponibles dans le filtre, mais seulement si on les coche.

## Août 2026 — Bonus et rareté des équipements

Les fiches équipement (Minimal, Line, Full, tableau) lisent les **bonus**
(`items.bonus`, repli sur `effect`). Les libellés de rareté sont les mêmes
partout : Commun, Peu commun, Rare, Très rare, Légendaire, Unique (0 à 5).

## Août 2026 — CVE-2026-13149 brace-expansion

`brace-expansion` (via `minimatch` / `glob` / ESLint) est forcé en 1.1.18, 2.1.4 et 5.0.9 dans `pnpm.overrides`, au-dessus des correctifs 1.1.16 / 2.1.2 / 5.0.7. Ça évite le DoS O(2ⁿ) sur des groupes `{}` non expansifs.

## Août 2026 — CI MySQL : TEXT sans DEFAULT

MySQL 8 refuse un DEFAULT SQL sur TEXT/BLOB/JSON (`SQLSTATE 1101`). Après le JSON `notification_channels`, les résistances fixes créature (`res_fixe_*`) bloquaient encore `php artisan migrate` en CI. Le défaut `'0'` est désormais dans `Creature::$attributes`.

## Août 2026 — GitHub : une seule `main`, Dependabot cadré

Le dépôt n’a plus qu’une branche active (`main`). Les PR Cursor et l’ancienne PR Vitest 4 (base trop vieille) sont fermées. Les alertes Dependabot déjà corrigées restent en « fixed ». Un fichier `.github/dependabot.yml` lance des mises à jour weekly npm (groupe minor+patch), Composer (PR individuelles) et Actions (minor+patch) ; Vitest 4 et les majors d’actions restent un chantier dédié.

## Août 2026 — Équipements monstre et menu d’options

Les fiches monstre (Minimal, Line, Full) listent les équipements de la créature
s’il y en a, sur le même modèle que les sorts (nom + aperçu Minimal). Le menu
d’options à côté du titre n’affiche que les raccourcis qui tiennent ; le reste
passe dans le « ⋮ », pour toutes les vues d’entités.

## Août 2026 — Aperçu sort depuis un monstre

Ouvrir un sort depuis la fiche minimale d’un monstre montre enfin les effets (chips inclus dans le payload, sans dépendre d’un second fetch). Le menu d’options suit la ligne du titre (raccourcis + overflow).

## Août 2026 — Tableaux d’entités + aperçu des sorts

Les filtres, le tri et la recherche des catalogues marchent à nouveau : multi-sélection (`whereIn`), application automatique, alias de tri (type d’objet, niveau de créature), recherche serveur et repli sur le nom en client.

## Août 2026 — Tableau objets plus lisible

Le catalogue d’équipements (vue Colonnes) montre d’abord portrait, nom, niveau, type, rareté et bonus. Description, résumé et prix restent optionnels. L’état de publication n’apparaît que pour les éditeurs. Un survol des en-têtes explique chaque colonne. En vue Line, la recette n’encombre plus la liste.

## Août 2026 — CI MySQL, resolved-stats, tri catalogue monstres

Les migrations CI passent sur MySQL : plus de DEFAULT SQL sur `notification_channels` (JSON). Les stats runtime d’une créature suivent la visibilité du monstre/PNJ (un brouillon n’est plus lisible par id). Trier le catalogue monstres par nom ne provoque plus d’erreur SQL pour un non-admin.

## Août 2026 — Alertes Dependabot Node (extract-zip, undici)

Les alertes GitHub sur `extract-zip` et `undici` (CRLF, cookies, retry, keep-alive, blob) sont traitées : Puppeteer inutilisé est retiré ; `undici` transitif du SDK Cursor est forcé en 6.28.0. D’autres paquets jamais importés ont aussi été enlevés (`@playwright/mcp`, `mysql-mcp`, Pikaday, Precognition, Font Awesome npm, etc.). La config MCP Cursor locale n’est plus versionnée.

## Août 2026 — Cadrage de l’IA générative

Le besoin (sorts/monstres trop « Dofus », PNJ trop combinatoires pour un algo) est documenté dans `docs/IA/` : pas de modèle entraîné au départ, Laravel prépare le contexte, l’IA propose en file d’attente, les objets se réduisent par grille algorithmique, monstres et PNJ se génèrent à la demande (monstre + sorts dans le même paquet). Rien n’est encore branché au site.

## Août 2026 — Fiches créature : mods, CA, compétences

L’affichage des caractéristiques monstre (Minimal / Line / Full) met les
**modificateurs** en avant, calcule la CA / l’initiative via formules + runtime
(équipements inclus), empile score → mod → sauvegarde, libelle les résistances
relatives (sans les 0 %), et montre les compétences groupées par stat.

Le **Dommage fixe Multiples (DO mult.)** a maintenant une colonne composable
(`do_fixe_multiple`) et apparaît dans le groupe Dommages (y compris à 0, ou via
équipements / runtime).

## Août 2026 — PDF multi + index server-side (gros volumes)

Les exports PDF multi-entités respectent la même visibilité que les listes
(`visibleToUser`). Les index items / monstres / ressources / consommables /
conditions paginent côté serveur (plus de plafond silencieux à 5000).

## Août 2026 — Optimisation & polish monstres

Pass type « sorts » côté monstres : validation race (`monster_races`), PDF multi
filtré par visibilité, API tableau allégée (plus d’arbre d’effets de sorts par
ligne), catalogues d’édition bornés + recherche API relations, vue Full enrichie
(sorts, empty states), Line alignée (traits / PA boss).

## Août 2026 — Bibliothèques : plus de plafond à 50 fiches

Les tableaux embarqués dans les pages Bibliothèque (CMS) paginent côté serveur :
on voit l’ensemble du scrap (milliers de sorts / objets / etc.), plus seulement
les 50 premières lignes (qui donnaient l’impression de n’avoir que 2 pages).

## Août 2026 — Qualité effets « autre » + listes sécurisées

Les téléports déjà mappés en `déplacer` mais restés en `autre` se reclasse via
`scrapping:effects:reapply-mappings` (sans re-import). Le remboursement de PA
(Dofus 120) est mappé en booster PA. L’invisibilité (150) devient l’état
`Invisible` via `appliquer-etat`. L’API des définitions d’effet est paginée.
Les tables legacy `spell_effects` / `spell_effect_types` sont retirées. Les
listes Inertia et tableaux API filtrent la visibilité comme la policy `view`.

## Août 2026 — Polish UI sorts (usage unifié)

Minimal et Line partagent `SpellUsageBlock` (méta, résolution, chips). La fiche Full
aligne l’utilisation sur cette méta, ajoute un bandeau d’effets au-dessus du journal,
et clarifie les empty states. L’éditeur d’effets signale les modifications non
enregistrées (badge dirty).

## Août 2026 — Visibilité liste sorts + perf area

L’API tableau des sorts (et l’index / PDF multi) ne renvoie plus les brouillons
ou contenus hors droits : filtre aligné sur la policy `view`. La zone (`area`)
d’un sort réutilise les effets déjà chargés, sans requête SQL par ligne.
L’édition des champs suit la policy (auteur ou admin). Le tri `po`/`area` utilise
des colonnes ou une sous-requête valides. La sync des classes conserve les
emplacements pivot existants. L’éditeur de sort ne charge plus toutes les
définitions d’effet : recherche via API. Le legacy `spellEffects` n’est plus
exposé dans la resource sort.

## Août 2026 — Affichage des états de sorts

Les noms d’états issus de DofusDB qui arrivaient sous forme d’hyperlien
(`{{spell,…::Évadé}}`) s’affichent désormais avec le libellé lisible uniquement.
Nettoyage à l’import, à la résolution des effets, côté interface, et commande de
maintenance pour les données déjà en base.

## Août 2026 — Nettoyage des images orphelines

Les super-admins peuvent lancer depuis le menu maintenance (**Fichiers orphelins**) un scan
des fichiers MediaLibrary sans référence en base, suivre la progression, annuler, et recevoir
une notification du nombre de fichiers supprimés. La suppression définitive d’une entité
continue de purger ses images ; la mise en corbeille les conserve pour permettre la
restauration. Une tâche cron optionnelle (désactivée par défaut) peut enfiler le même job.

## Août 2026 — Favoris en base + modal / page

Les favoris sont enregistrés en base pour chaque compte. Un bouton cœur dans le header ouvre
un modal (sans quitter la page) avec les fiches groupées par type et une recherche pour en
ajouter ; « Ouvrir en page » mène à `/favoris`. Sans connexion, un message invite à se connecter.
L’icône des menus d’options est un cœur plein ou vide. La recherche globale et les pickers
affichent d’abord les favoris.

## Août 2026 — Refonte vues entités + caractéristiques

Parcours unifié : minimal → overlay → modal → page. Actions réordonnées (presets), DofusDB
accessible dès le minimal déployé. Caractéristiques : densités `icon` / `labeled` / `spacious`,
groupes canoniques, résumé 5 stats. Épinglage multi-fenêtres flottantes. Favoris locaux et
refresh DofusDB (droit d’écriture) branchés côté monstre / entités scrappables.

Extension multi-entités : shell `useEntityMinimalShell` + titre modal sur sorts, objets,
consommables, ressources, races, panoplies, capacités ; PNJ avec stats créature. Tous les
Minimal (condition, trait, spé, shop, campagne, scénario, type de ressource…) passent par
`EntityMinimalCard` (hors `language`).

## Août 2026 — Caractéristiques calculées (base / objets / contexte)

Les fiches de créatures peuvent désormais composer une caractéristique à partir de sa formule
de base, des bonus d’équipement et d’un bonus contextuel (nombre ou formule). Un total stocké
reste prioritaire pour ne pas casser le scrap. Le niveau peut être une fourchette ou un dé :
l’interface affiche la première valeur possible et recalcule toute la fiche via un sélecteur.
Un popover détaille la décomposition et le tableau par niveau. Les formulaires monstre / objet /
sort ont été allégés (sections repliables).

## Août 2026 — Gates et préparation scrap serveur

Le pipeline d’import est verrouillé pour un scrap catalogue complet :

- audits live : conversion effets à 100 %, 0 mapping caractéristique manquant ; ~33 % d’effets `autre`
  surtout glyphes / pièges / placeholders (hors périmètre volontaire) ;
- 80 écarts qualité objet corrigés (`item_type_dofus_ids` dérivés des aides) ;
- quality gate **active par défaut** sur `scrapping:run` (hors simulate) + gate effets après import sorts ;
- samples créature / objet alignés sur les formules + tests automatiques ;
- mappings téléports / échanges ajoutés ; checklist scrap serveur documentée ;
- workflow CI dédié aux gates scrapping.

## Août 2026 — Référence DofusDB pendant l’édition

Sur les fiches **Full** et les écrans d’**édition** (page ou modal), une action avec l’icône Dofus
ouvre un **panneau flottant** de référence lorsque l’entité a un `dofusdb_id`. Le panneau affiche
le lien profond vers DofusDB et ouvre le site dans une fenêtre séparée (pas d’iframe), sans bloquer
l’édition de la fiche Krosmoz.

## Août 2026 — Almanax interactif dans le header

Le badge Almanax du header affiche désormais le **jour** (identique au calendrier réel) avec le mois
Dofus, un **tooltip** au survol, et un **calendrier** au clic pour parcourir les mois du Monde des Douze.

## Août 2026 — Conversion Dofus plus fiable

Le socle d'import Dofus a été simplifié pour rendre les imports massifs plus sûrs :

- les valeurs numériques suivent désormais les formules et limites administrables des caractéristiques ;
- un élément incorrect n'interrompt plus tout un lot ;
- les effets et caractéristiques inconnus sont signalés pour revue au lieu d'être perdus ;
- une commande d'audit vérifie le paramétrage avant un import massif ;
- l'administration indique les mappings incomplets et les prévisualisations exposent les étapes de conversion.

Cette évolution ne cherche pas à deviner toutes les exceptions Dofus. Elle privilégie une donnée brute
importable et clairement signalée lorsqu'une règle métier doit encore être définie.

### Fiches de sort enrichies

Les fiches de sort conservent maintenant les contraintes globales de lancer en ligne ou en diagonale, le
mode direct/piège/glyphe, la limite de cumul et le temps de relance global. Ces informations sont importées
depuis le premier niveau DofusDB et restent séparées du moteur détaillé des effets.

### Première calibration des monstres

Le niveau des monstres suit maintenant une conversion lisible : dix niveaux Dofus correspondent à
un niveau Krosmoz, dans une plage de 1 à 30. Une analyse des statistiques de l'ensemble du catalogue
DofusDB sert de base à la calibration progressive des autres caractéristiques.

Les autres caractéristiques des monstres disposent désormais de leurs propres marges : scores jusqu'à 30,
PA/PM/PO élargis, bonus tactiques conservés et initiative non plafonnée. Les résistances en pourcentage
ont été remplacées par cinq paliers faciles à jouer, tandis que critiques et soins suivent des conversions
simples et limitées. Les Kamas ne sont plus importés sur les monstres.

La chaîne d'import conserve maintenant aussi la description, le tacle, la fuite, les critiques et les soins.
Les races DofusDB sont reliées par leur identifiant source (et créées en brouillon si nécessaire). Les règles
JSON, leur copie de secours PHP et la base sont synchronisées et contrôlées par les audits automatisés.

### Calibration des objets

Les bonus d'équipement Dofus conservent désormais leurs malus : les conversions sont symétriques autour
de zéro. Les plafonds distinguent l'équipement de la forgemagie, notamment ±6 pour les caractéristiques
principales, ±5 PA et ±2 PM hors forgemagie. Les résistances relatives ne sont transformées en paliers que
pour les panoplies. Les faux PV issus de l'identifiant technique DofusDB 0 sont ignorés et les dommages
multi-éléments utilisent leur véritable identifiant source.

La calibration objet a ensuite été consolidée : chaque bonus réservé à un emplacement pointe vers le bon
type DofusDB, les grilles et exemples respectent leurs plafonds et reproduisent exactement les formules.
Les PV et l'initiative restent non plafonnés, le critique accepte les malus jusqu'à -3 et les résistances
fixes des boucliers sont limitées à ±7 hors forgemagie. Ces règles sont synchronisées en base et couvertes
par les contrôles automatiques.

### Calibration des sorts

Les caractéristiques de sorts utilisent maintenant des unités Krosmoz cohérentes avant leur branchement
au système d'effets : niveau 1–20, limites jouables de portée et de cadence, critique signé de −3 à +3,
résistances relatives par paliers et résistances fixes plafonnées à 10. Les dégâts fixes, soins, bonus de
portée et initiative ont également été harmonisés avec les créatures et les objets.

Cinq métadonnées manquantes ont été définies : lancement en ligne, lancement en diagonale, type de cible,
cumul maximal et délai global. Un contrôle automatique vérifie désormais les bornes, les exemples et
l'invariant « zéro reste zéro » sur l'ensemble des définitions de sorts.

La conversion des effets a ensuite été sécurisée : les plages DofusDB ne sont plus confondues avec des
jets de dés, et les formules exécutées en jeu utilisent désormais les valeurs Krosmoz converties, y compris
en critique. Les bonus, retraits et vols sont distingués correctement ; les paliers de résistance restent
valides et le vol de vie utilise sa courbe réduite dédiée.

Les dégâts, soins et vols de vie sont maintenant proportionnés aux PV de référence du niveau. Le budget
total d'un tour est réparti selon le coût en PA, la portée, la zone et les différentes lignes d'effet du
sort, sur la base du premier équilibrage fourni dans « Creation sort ».

Les effets de bouclier DofusDB sont enfin reconnus comme `protéger` (et non plus comme boosts génériques),
puis budgétés comme les soins pour rester proportionnés aux PV.

La résolution des sorts (jet d'attaque, sauvegarde ou réussite auto) et le booléen Wakfu/physique sont
désormais déduits des effets convertis, car DofusDB ne fournit pas `isMagic`. Les DD de sauvegarde suivent
la formule des règles : 8 + modificateur + maîtrise.

Les états (`appliquer-etat` / `s-appliquer-etat`) propagent correctement leur durée vers
`duration_formula`, mettent à jour les pivots existants au réimport, et alimentent le référentiel
`Condition` avec leurs flags (immobilisation, invulnérabilité, etc.).

Les déplacements distinguent mieux push / pull / téléport (y compris « sans dommages »), et le triage
des mappings restants sépare les clés jouables des effets hors périmètre documentés.

Le snapshot des mappings scrapping a été reconstruit depuis les JSON d’entité (clés item/spell
réalignées). L’audit ignore les catalogues sans cible (`monster-race`). Les sorts encore présents
sur DofusDB ont été resynchronisés : résolution inférée, DD en `8 + …`, couverture d’effets à 100 %.

Les PV temporaires de sort sont désormais distincts du bouclier : sous-effet `donner-pv-temporaires`,
caractéristique `pv_temporaires_spell` (charac DofusDB 95), même enveloppe de budget que les soins.
Le max PV via sort reste porté par la vitalité.

Les compétences actives (18) peuvent être boostées ou retirées par un effet de sort
(`acrobatics_spell`, `stealth_spell`, etc.), sur le même modèle que l’équipement, plafonnées à ±5.

Les sorts locaux absents de l’API DofusDB (404, ex. anciennes flèches Cra) ont été passés en
`archived` avec `auto_update=false` : conservés pour historique, exclus des resync automatiques.
