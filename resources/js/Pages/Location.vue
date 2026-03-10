<script setup>

import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import { Head, router } from '@inertiajs/vue3';

import AuthLayout from '@/Layouts/AuthLayout.vue';
import { useLocationStore } from '@/stores/location';

const location = useLocationStore();

const HandleLocationChanged = (place) => {
    console.log('handelLocationchanged', place)
    location.$patch({
        destination: {
            name: place.name,
            address: place.formatted_address,
            geometry: {
                lat: place.geometry.location.lat(),
                lng: place.geometry.location.lng()
            }
        }
    })
};

const handleSelectLocation = () => {
    if(location.destination.name != '') {
        router.visit('/map');
    }
}

</script>

<template>
    <AuthLayout>
        <Head title="Log in" />

        <form @submit.prevent="">
            <div>
                <InputLabel value="Where are you going?" />

                <GMapAutocomplete
                    placeholder="my destination"
                    @place_changed="HandleLocationChanged"
                    class="block w-full"
                    >
                </GMapAutocomplete>

            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton @click.prevent="handleSelectLocation" class="ms-4"  >
                    Find a Ride
                </PrimaryButton>
            </div>
        </form>

    </AuthLayout>
</template>
