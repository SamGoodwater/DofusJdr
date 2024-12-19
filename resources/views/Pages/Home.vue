<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Btn from '../Components/actions/Btn.vue';
import { IconsGetter } from '../../js/Utils/IconsGetter';
import Icons from '../Components/images/Icon.vue';

const iconPA = ref('');
const iconPM = ref('');
const iconPO = ref('');
const iconTacle = ref('');
const iconRes_terre = ref('');

onMounted(async () => {
    iconPA.value = await IconsGetter.get('modules', 'pa');
    iconPM.value = await IconsGetter.get('modules', 'pm');
    iconPO.value = await IconsGetter.get('modules', 'po');
    iconTacle.value = await IconsGetter.get('modules', 'tacle');
    iconRes_terre.value = await IconsGetter.get('modules', 'res_terre');
});

const convertStability = {
    alpha: "α",
    beta: "β",
    rc: "rc",
    stable: ""
};

const appDescription = ref(import.meta.env.VITE_APP_DESCRIPTION);
const appKeywords = ref(import.meta.env.VITE_APP_KEYWORDS);

const page = usePage();

</script>

<template>

    <section class="prose prose-state prose-a:text-secondary max-md:prose-sm lg:prose-lg">

        <h1 class="text-amber-800 text-center">Site en construction</h1>

        <!-- TESTS BUTTONS -->
        <div class="text-center">
            <div class="flex gap-4 justify-content-around">
                <Btn theme="main glass sm" label="Principale" />
                <Btn theme="minor glass sm" label="Mineure" />
                <Btn theme="validate glass sm" label="Valider" />
                <Btn theme="cancel glass sm" label="Annuler" />
                <Btn theme="simple glass sm" label="Simple" />
            </div>
            <div class="flex mt-4 gap-4 justify-content-around">
                <Btn theme="outline sm main">
                    <template #label>
                        Principale
                    </template>
                </Btn>
                <Btn theme="outline sm minor">
                    <template #label>
                        Mineure
                    </template>
                </Btn>
                <Btn theme="outline sm validate">
                    <template #label>
                        Valider
                    </template>
                </Btn>
                <Btn theme="outline sm cancel">
                    <template #label>
                        Annuler
                    </template>
                </Btn>
                <Btn theme="outline sm simple">
                    <template #label>
                        Simple
                    </template>
                </Btn>
            </div>
            <div class="flex mt-4 gap-4 justify-content-around">
                <Btn theme="link sm main">
                    <template #label>
                        Principale
                    </template>
                </Btn>
                <Btn theme="link sm minor">
                    <template #label>
                        Mineure
                    </template>
                </Btn>
                <Btn theme="link sm validate">
                    <template #label>
                        Valider
                    </template>
                </Btn>
                <Btn theme="link sm cancel">
                    <template #label>
                        Annuler
                    </template>
                </Btn>
                <Btn theme="link sm simple">
                    <template #label>
                        Simple
                    </template>
                </Btn>
            </div>

        <!-- Tests icones -->
            <p>
                <Icons theme="xs" :source="iconPA" /> XS,
                <Icons theme="sm" :source="iconPM" /> SM,
                <Icons theme="md" :source="iconPO" /> MD,
                <Icons theme="lg" :source="iconTacle" /> LG,
                <Icons theme="xl" :source="iconRes_terre" /> XL
            </p>
            <p>
                <Icons theme="md" :source="iconPO" /> MD,
                <Icons theme="lg" :source="iconTacle" /> LG,
                <Icons theme="xl" :source="iconRes_terre" /> XL
            </p>
        </div>

        <h3 id="bienvenue-dans-krosmozjdr-l-aventure-pique-dans-l-univers-du-monde-des-douze-">Bienvenue dans
            <strong>KrosmozJDR</strong>, l’aventure épique dans l’univers du monde des Douze !
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
