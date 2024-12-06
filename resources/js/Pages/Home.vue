<script setup>
import { ref } from 'vue';

const convertStability = {
    alpha: "α",
    beta: "β",
    rc: "rc",
    stable: ""
};

const appName = ref(import.meta.env.VITE_APP_NAME);
const appVersion = ref(import.meta.env.VITE_APP_VERSION);
const appDescription = ref(import.meta.env.VITE_APP_DESCRIPTION);
const appStability = ref(convertStability[import.meta.env.VITE_APP_STABILITY]);
const appKeywords = ref(import.meta.env.VITE_APP_KEYWORDS);
</script>

<template>

    <Head title="Accueil">
        <meta head-key="description" name="description" :content="appDescription" />
        <meta head-key="keywords" name="keywords" :content="appKeywords + ', Accueil'" />
    </Head>
    <section class=" prose prose-state prose-a:text-secondary max-md:prose-sm lg:prose-lg">

        <button class="btn glass bg-main-800 hover:bg-main-600">
            Button
        </button>

        <h3 id="bienvenue-dans-krosmosjdr-l-aventure-pique-dans-l-univers-du-monde-des-douze-">Bienvenue dans
            <strong>KrosmosJDR</strong>, l’aventure épique dans l’univers du monde des Douze !
        </h3>
        <p>Plongez dans le monde des Douze, un univers riche et vibrant issu de l’imaginaire de <em>Dofus</em>, où
            l’aventure, la stratégie et la magie s’entrelacent pour créer une expérience unique de jeu de rôle. Ici,
            chaque partie est une porte ouverte vers des terres fascinantes, des créatures captivantes et des combats
            épiques.</p>
        <h4 id="explorez-des-lieux-mythiques">Explorez des lieux mythiques</h4>
        <p>De la cité lumineuse de Bonta aux mystères d’Astrub, en passant par les plaines sauvages des Craqueleurs et
            les secrets d’Amakna, découvrez un monde vaste et varié où chaque région est le théâtre d’histoires
            légendaires. Préparez-vous à croiser la route des Wabbits malicieux, des Bouftous sauvages, des Chafeurs
            redoutables, et à affronter les puissants maîtres des donjons comme Kardorim, Groloum ou encore le Comte
            Harebourg.</p>
        <h4 id="incarnez-une-classe-iconique">Incarnez une classe iconique</h4>
        <p>Choisissez parmi une grande variété de classes emblématiques : serez-vous un Crâ précis et implacable, un Iop
            téméraire et valeureux, un Osamodas lié aux créatures, ou encore un Eliotrope maître des portails ? Que vous
            soyez un soigneur Eniripsa, un stratège Roublard ou un manipulateur du temps Xélor, chaque classe offre des
            mécaniques uniques et des sorts adaptés à l’univers du jeu de rôle.</p>
        <h4 id="d-veloppez-votre-personnage">Développez votre personnage</h4>
        <p>Grâce à un système de compétences et d’aptitudes inspiré de D&amp;D, personnalisez votre héros pour qu’il
            reflète votre style de jeu. Enrichissez vos stratégies avec des spécialisations uniques, inspirées des
            subtilités des races de D&amp;D, et explorez des gameplays toujours plus variés et profonds.</p>
        <h4 id="un-gameplay-enrichi-et-immersif">Un gameplay enrichi et immersif</h4>
        <p>Notre jeu de rôle fusionne les règles classiques du JdR avec l’univers riche de Dofus, pour offrir une
            expérience immersive et accessible à tous, débutants comme vétérans. Préparez-vous à vivre des quêtes
            épiques, à forger des alliances mémorables, et à écrire votre légende dans un monde en perpétuelle
            évolution.</p>
        <hr>
        <h3 id="-tes-vous-pr-t-rejoindre-l-aventure-">Êtes-vous prêt à rejoindre l’aventure ?</h3>
        <p>Rassemblez vos compagnons, lancez vos dés et partez à la découverte du monde des Douze. Votre destin est
            entre vos mains !</p>
    </section>

</template>
