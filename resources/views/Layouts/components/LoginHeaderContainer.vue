<script setup>
import Btn from '../../Components/actions/Btn.vue';
import Route from '../../Components/text/Route.vue';
</script>

<template>
    <div class="flex flex-col text-center">
        <Route route='login' class="pt-1 px-4">
            <Btn theme="link lg" color="minor-200" label="Se connecter" tooltipPosition="left"
                tooltip="S'identifier à l'aide de son compte" />
        </Route>

        <Route route='register' class="pb-1 px-4">
            <Btn theme="simple link md" label="S'inscrire" tooltipPosition="left" tooltip="Créer un compte" />
        </Route>
    </div>
</template>
