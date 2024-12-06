<script setup>
</script>

<template>
    <div class="">
        <div class="hover:text-secondary/60">
            <Link :href="route('auth.login')">
            Se connecter
            </Link>
        </div>
        <div class="text-secondary/50 hover:text-secondary/75 text-sm">
            <Link :href="route('auth.register')">
            S'enregistrer
            </Link>
        </div>
    </div>
</template>
