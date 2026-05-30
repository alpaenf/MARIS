<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
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
        <Head title="Log in" />

        <div v-if="status" class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-slate-900">Masuk ke MARIS</h2>
                <p class="mt-1 text-xs text-slate-500">Kelola analisis mangrove dan laporan restorasi dalam satu dashboard.</p>
            </div>
            <div class="mb-4">
                <a
                    href="/auth/google"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:border-teal-300 hover:text-teal-700"
                >
                    <span class="text-sm">G</span>
                    Masuk dengan Google
                </a>
                <div class="mt-3 flex items-center gap-3 text-[10px] text-slate-400">
                    <span class="h-px w-full bg-slate-200"></span>
                    atau
                    <span class="h-px w-full bg-slate-200"></span>
                </div>
            </div>
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-slate-200"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-xl border-slate-200"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-3 flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-xs text-slate-500">Ingat saya</span>
                </label>
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-xs text-teal-700 hover:text-teal-600"
                >
                    Lupa password?
                </Link>
            </div>

            <div class="mt-4 flex flex-col gap-3">
                <PrimaryButton
                    class="w-full justify-center rounded-xl bg-teal-700 px-4 py-2.5 text-xs font-semibold text-white hover:bg-teal-600"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Masuk
                </PrimaryButton>
                <p class="text-center text-xs text-slate-500">
                    Belum punya akun?
                    <Link class="text-teal-700 hover:text-teal-600" :href="route('register')">Daftar sekarang</Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
