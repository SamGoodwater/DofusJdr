<script setup>
import Checkbox from '@/Components/inputs/Checkbox.vue';
import InputError from '@/Components/inputs/InputError.vue';
import InputLabel from '@/Components/inputs/InputLabel.vue';
import Btn from '@/Components/actions/Btn.vue';
import TextInput from '@/Components/inputs/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Se connecter" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" placeholder="exemple@exemple.fr" v-model="form.email" required
                    autofocus autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput id="password" type="password" placeholder="*************" v-model="form.password" required />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <Checkbox name="remember" v-model:checked="form.remember">
                    <span class="ms-2 text-sm text-gray-600">Se rappeler de mes identifiants</span>
                </Checkbox>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Mot de passe oublié ?
                </Link>

                <Btn class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Se connecter
                </Btn>
            </div>
        </form>
    </GuestLayout>
</template>
