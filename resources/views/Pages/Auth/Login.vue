<script setup>
import Checkbox from '../../Components/inputs/Checkbox.vue';
import InputError from '../../Components/inputs/InputError.vue';
import InputLabel from '../../Components/inputs/InputLabel.vue';
import Btn from '../../Components/actions/Btn.vue';
import Route from '../../Components/text/Route.vue';
import TextInput from '../../Components/inputs/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('auth.login'), {
        onFinish: () => form.reset('password'),
    });
};

onMounted(() => {
    document.title = 'Connexion';
});
</script>

<template>

    <form @submit.prevent="submit">
        <div>
            <InputLabel for="email" value="Email" />
            <TextInput id="email" type="email" placeholder="exemple@exemple.fr" v-model="form.email" required autofocus
                autocomplete="username" />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="mt-4">
            <InputLabel for="password" value="Password" />
            <TextInput id="password" type="password" placeholder="*************" v-model="form.password" required />
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4 block">
            <Checkbox theme="sm gray-600" class="ms-2" name="remember" v-model="form.remember"
                label="Se rappeler de mes identifiants">
            </Checkbox>
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Route route='password.request'>
                <Btn theme="link sm gray-600"> Mot de passe oublié ? </Btn>
            </Route>

            <Btn theme="main-600 glass" class="ms-4" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                Se connecter
            </Btn>
        </div>
    </form>
</template>
