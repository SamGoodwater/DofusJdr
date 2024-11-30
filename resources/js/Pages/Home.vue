<script setup>
import Layout from '@/Layouts/Default.vue';
defineOptions({
    layout: Layout
});
</script>

<template>

    <Head title="Accueil">
        <meta head-key="description" name="description" content="Page d'accueil" />
        <meta head-key="keywords" name="keywords" content="Accueil, JDR, Jeu de Rôle, Dofus" />
    </Head>
    <h1>Home</h1>
    <p>
        <Link :href="route('auth.login')">
        Login
        </Link>
        <Link :href="route('auth.register')">
        Register
        </Link>
    </p>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis adipisci, ex pariatur inventore voluptas
        blanditiis. Amet tempora rerum odit hic dicta, ullam illum facere repellendus natus nesciunt saepe, neque
        placeat! Reprehenderit hic a odio, consequuntur quae repudiandae totam perspiciatis inventore nisi quia
        excepturi quis sequi, iusto ab eum? Itaque ipsa, quas laudantium, ducimus quasi nostrum tempore fugiat
        dignissimos laboriosam alias blanditiis, quisquam tempora iste ea quia libero obcaecati! Doloremque reiciendis
        voluptate unde magni quaerat explicabo nam libero, possimus excepturi modi ipsa quibusdam consectetur enim quod
        quas? Dolorum consequatur, error voluptatem consectetur ea nobis provident nemo maxime rerum id, fuga molestias
        repudiandae. Nesciunt, incidunt dignissimos. Libero hic magni alias veritatis impedit dolore, unde, tenetur
        molestiae quos autem blanditiis nostrum reprehenderit doloribus modi dolor iusto enim aut sapiente, praesentium
        facere? Aspernatur harum provident magni quibusdam inventore corporis neque doloribus qui obcaecati laudantium
        nisi nostrum dolores, possimus cumque vitae adipisci accusamus! Blanditiis eveniet omnis odit quis natus dolore
        nihil doloremque, quaerat iste quasi quidem, enim cupiditate ut totam exercitationem consequatur doloribus
        numquam porro cumque alias. Eveniet incidunt atque, vero facilis rem natus eaque nemo non harum, accusantium
        animi quasi maxime magni ad! Provident corporis ducimus ea voluptatibus vitae. Harum ad eaque laboriosam velit!
    </p>
</template>
