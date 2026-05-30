<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div class="mb-3">
                <h2 class="text-2xl font-semibold text-slate-900">Buat Akun MARIS</h2>
                <p class="mt-1 text-sm text-slate-500">Mulai akses data risiko pesisir dan rekomendasi restorasi.</p>
            </div>
            <div class="mb-4">
                <a
                    href="/auth/google"
                    class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:border-teal-300 hover:text-teal-700"
                >
                    <span class="text-base">G</span>
                    Daftar dengan Google
                </a>
                <div class="mt-3 flex items-center gap-3 text-xs text-slate-400">
                    <span class="h-px w-full bg-slate-200"></span>
                    atau
                    <span class="h-px w-full bg-slate-200"></span>
                </div>
            </div>
            <div class="grid gap-3 lg:grid-cols-2">
                <div>
                    <InputLabel for="name" value="Nama" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full rounded-xl border-slate-200"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full rounded-xl border-slate-200"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-xl border-slate-200"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel
                        for="password_confirmation"
                        value="Konfirmasi Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full rounded-xl border-slate-200"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>
            </div>

            <div class="mt-3 flex flex-col gap-3">
                <PrimaryButton
                    class="w-full justify-center rounded-xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-600"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Daftar
                </PrimaryButton>
                <p class="text-center text-sm text-slate-500">
                    Sudah punya akun?
                    <Link class="text-teal-700 hover:text-teal-600" :href="route('login')">Masuk</Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
