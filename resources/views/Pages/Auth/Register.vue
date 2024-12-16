<script setup>
import InputError from '../../Components/inputs/InputError.vue';
import InputLabel from '../../Components/inputs/InputLabel.vue';
import Btn from '../../Components/actions/Btn.vue';
import Route from '../../Components/text/Route.vue';
import TextInput from '../../Components/inputs/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
onMounted(() => {
    document.title = 'Inscription';
});
</script>

<template>

    <form @submit.prevent="submit">
        <div>
            <InputLabel for="name" value="Name" />

            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                autocomplete="name" />

            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="mt-4">
            <InputLabel for="email" value="Email" />

            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                autocomplete="username" />

            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="mt-4">
            <InputLabel for="password" value="Password" />

            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                autocomplete="new-password" />

            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
            <InputLabel for="password_confirmation" value="Confirm Password" />

            <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                v-model="form.password_confirmation" required autocomplete="new-password" />

            <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Route route='login'>
                <Btn theme="link md main-600" label="Déjà inscrit ?" />
            </Route>

            <Route route="register">
                <Btn theme="main-600 glass" class="ms-4"
                    :disabled="form.processing" label="S'enregistrer" />
            </Route>
        </div>

    </form>

</template>
