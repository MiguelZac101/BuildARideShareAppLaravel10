<template>
    <GuestLayout>
        <Head title="Driver Details" />


            <h1 class="text-3xl font-semibold mb-4">Driver and car details</h1>
            <form action="#" @submit.prevent="handleSaveDriver">
                <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                    <div class="bg-white px-4 py-5 sm:p-6">
                        <div>
                            <input type="text" name="name" id="name" v-model="driverDetails.name" placeholder="Full Name"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                                <div v-if="errors.name">{{ errors.name}}</div>
                        </div>
                        <div class="mt-2">
                            <input type="number" name="year" id="year" v-model="driverDetails.year" placeholder="Car Year"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                            <div v-if="errors.year">{{ errors.year}}</div>
                        </div>
                        <div class="mt-2">
                            <input type="text" name="make" id="make" v-model="driverDetails.make" placeholder="Make"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                            <div v-if="errors.make">{{ errors.make}}</div>
                        </div>
                        <div class="mt-2">
                            <input type="text" name="model" id="model" v-model="driverDetails.model" placeholder="Model"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                            <div v-if="errors.model">{{ errors.model}}</div>
                        </div>
                        <div class="mt-2">
                            <input type="text" name="color" id="color" v-model="driverDetails.color" placeholder="Color"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                            <div v-if="errors.color">
                                {{ errors.color}}
                            </div>
                        </div>
                        <div class="mt-2">
                            <input type="text" name="license_plate" v-model="driverDetails.license_plate" id="license_plate"
                                placeholder="License Plate #"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                            <div v-if="errors.license_plate">{{ errors.license_plate}}</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton @click.prevent="handleSaveDriver" class="ms-4"  >
                            Continue
                        </PrimaryButton>
                    </div>
                </div>
            </form>
    </GuestLayout>

</template>
<script setup>
import { reactive, ref } from 'vue'
import http from '@/helpers/http'
import { router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

import PrimaryButton from '@/Components/PrimaryButton.vue';

const driverDetails = reactive({
    name: '',
    year: null,
    make: '',
    model: '',
    color: '',
    license_plate: ''
});

const errors = ref({});

const handleSaveDriver = () => {
    http().post('/api/driver', driverDetails)
        .then((response) => {
            router.visit('/standby');
        })
        .catch((err) => {
            console.error(err);
            errors.value = err.response.data.errors;
            console.log(errors.value);
        })
}




</script>
