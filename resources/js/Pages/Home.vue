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
    <section class=" prose prose-stone prose-a:text-secondary sm:prose-md lg:prose-lg">
        <h1>Home</h1>
        <p>
            <Link :href="route('auth.login')">
            Login
            </Link>
            <Link :href="route('auth.register')">
            Register
            </Link>
        </p>
        <button class="btn bg-primary hover:bg-base-300">
            Button
        </button>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eligendi quas iure cupiditate facilis placeat quidem
            maiores, commodi quibusdam nam, adipisci assumenda repudiandae repellendus doloremque esse, rem inventore
            molestiae? Perspiciatis, consequatur aperiam hic, nesciunt mollitia consectetur expedita fugiat optio
            doloremque dignissimos pariatur vel nam reprehenderit numquam libero eos! Recusandae placeat mollitia
            dolores, fuga soluta sint? Tempore corrupti soluta ea commodi, consequatur qui optio id aperiam minima
            nostrum. Maxime perspiciatis, facilis velit eveniet ex dicta fugit eos quae omnis eaque sapiente dolor odio
            beatae laudantium quam! Odit incidunt voluptates harum, maxime beatae perferendis maiores exercitationem at
            facilis distinctio iste earum itaque et?</p>
    </section>

</template>
