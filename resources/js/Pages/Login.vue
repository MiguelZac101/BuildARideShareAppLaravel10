<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';

import { vMaska } from 'maska/vue';
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';

import { router } from '@inertiajs/vue3';

const credentials = reactive({
    phone: null,
    login_code: null
});

const waitingOnVerification = ref(false);

onMounted(() => {
    if(localStorage.getItem('token')) {
        router.visit('/landing');
    }
});

const getFormattedCredentials = () => {
    return {
        phone: credentials.phone.replaceAll(' ', ''),
        login_code: credentials.login_code
    }
};

const handleLogin = () => {
    axios.post('/api/login', getFormattedCredentials())
        .then((response) => {
            console.log(response.data)
            waitingOnVerification.value = true;
        })
        .catch((error) => {
            console.error(error)
            alert(error.response.data.message)
        })
};

const handleVerification = () => {
    axios.post('/api/login/verify', getFormattedCredentials())
        .then((response) => {
            console.log(response.data) //auth token
            localStorage.setItem('token', response.data)
            router.visit('/landing');
        })
        .catch((error) => {
            console.error(error)
            alert(error.response.data.message)
        })
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <form v-if="!waitingOnVerification" @submit.prevent="handleLogin">
            <div>
                <InputLabel for="phone" value="Phone" />

                <TextInput
                    id="phone"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="credentials.phone"
                    required
                    autofocus
                    autocomplete="phone"
                    placeholder="+51 912 345 678"
                    v-maska="'+51 ### ### ###'"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ms-4" >
                    Log in
                </PrimaryButton>
            </div>
        </form>

        <form v-else @submit.prevent="handleVerification">
            <div>
                <InputLabel for="login_code" value="login_code" />

                <TextInput
                    id="login_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="credentials.login_code"
                    required
                    autofocus
                    placeholder="123456"
                    v-maska="'######'"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ms-4" >
                    Verify
                </PrimaryButton>
            </div>
        </form>

    </GuestLayout>
</template>
