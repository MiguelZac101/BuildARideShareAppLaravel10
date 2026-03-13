<script setup>

import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import { Head, router } from '@inertiajs/vue3';

import AuthLayout from '@/Layouts/AuthLayout.vue';
import { useLocationStore } from '@/stores/location';
import { onMounted, ref } from 'vue';

import http from '@/helpers/http';

const location = useLocationStore();

const gMap = ref(null);

const handleConfirmTrip = () => {
    http().post('/api/trip', {
        origin: location.current.geometry,
        destination: location.destination.geometry,
        destination_name: location.destination.name
    })
        .then((response) => {
            router.visit('/trip/');
        })
        .catch((error) => {
            console.error(error)
        })
}

onMounted(async () => {
    //does the user have a location set?
    if(location.destination.name == '') {
        router.visit('/location');
    }

    //lets get the users current location
    await location.updateCurrentLocation();

    //draw a path an the map
    gMap.value.$mapPromise.then((mapObject) => {

        let currentPoint = new google.maps.LatLng(location.current.geometry),
            destinationPoint = new google.maps.LatLng(location.destination.geometry),
            directionsService = new google.maps.DirectionsService,
            directionsDisplay = new google.maps.DirectionsRenderer({
                map: mapObject
            })

        directionsService.route({
            origin: currentPoint,
            destination: destinationPoint,
            avoidTolls: false,
            avoidHighways: false,
            travelMode: google.maps.TravelMode.DRIVING
        }, (res, status) => {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(res)
            } else {
                console.error(status)
            }
        })
    })

});

</script>

<template>
    <AuthLayout>
        <Head title="Map" />

        <h1>here's your trip</h1>

        <form @submit.prevent="">
            <div>
                <div>
                    <GMapMap
                        :center="location.destination.geometry"
                        :zoom="11"
                        ref="gMap"
                        style="width: 100%; height: 256px;"
                    >
                        <GMapMarker :position="location.destination.geometry" />
                    </GMapMap>
                </div>
                <h2>Going to {{ location.destination.name }}</h2>
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton @click.prevent="handleConfirmTrip" class="ms-4"  >
                    Let's Go
                </PrimaryButton>
            </div>
        </form>

    </AuthLayout>
</template>
