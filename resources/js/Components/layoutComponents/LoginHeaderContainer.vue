<script setup>
</script>

<template>
    <div class="flex flex-col">
        <div class="hover:text-secondary/60 tooltip tooltip-left" data-tip="S'identifier à l'aide de son compte">
            <Link :href="route('auth.login')">
            Se connecter
            </Link>
        </div>
        <div class="text-secondary/50 hover:text-secondary/75 text-sm tooltip tooltip-left" data-tip="Créer un compte">
            <Link :href="route('auth.register')">
            S'inscrire
            </Link>
        </div>
    </div>
</template>
