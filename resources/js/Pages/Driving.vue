<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4 text-center">{{ title }}</h1>
        <div>
            <div v-if="!trip.is_complete" class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left" >
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap :zoom="14" :center="location.current.geometry" ref="gMap"
                            style="width:100%; height: 256px;">
                            <GMapMarker :position="location.current.geometry" :icon="currentIcon" />
                            <GMapMarker :position="location.destination.geometry" :icon="destinationIcon" />
                        </GMapMap>
                    </div>
                    <div class="mt-2">
                        <p class="text-xl">Going to <strong>pick up a passenger</strong></p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button v-if="trip.is_started"
                        @click="handleCompleteTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        Complete Trip</button>
                    <button v-else
                        @click="handlePassengerPickedUp"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        Passenger Picked Up</button>
                </div>
            </div>

            <div v-else class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left" >
                <div class="bg-white px-4 py-5 sm:p-6">
                    <Tada />
                </div>
            </div>

        </div>
    </div>
</template>
<script setup>

import http from '@/helpers/http';
import { useLocationStore } from '@/stores/location';
import { useTripStore } from '@/stores/trip';

import { onMounted, onUnmounted, ref } from 'vue'

import { router } from '@inertiajs/vue3'

const location = useLocationStore();
const trip = useTripStore();

const gMap = ref(null);
const intervalRef = ref(null);

const title = ref('Waiting for ride request...');

const currentIcon = {
    url: 'https://openmoji.org/data/color/svg/1F698.svg',
    scaledSize: {
        width: 32,
        height: 32
    }
}

const destinationIcon = {
    url: 'https://openmoji.org/data/color/svg/1F920.svg',
    scaledSize: {
        width: 24,
        height: 24
    }
}

const updateMapBounds = (mapObject) => {
    let originPoint = new google.maps.LatLng(location.current.geometry),
        destinationPoint = new google.maps.LatLng(location.destination.geometry),
        LatLngBounds = new google.maps.LatLngBounds();

        LatLngBounds.extend(originPoint);
        LatLngBounds.extend(destinationPoint);

        mapObject.fitBounds(LatLngBounds);
}

const broadcastDriverLocation = () => {
    http().post(`/api/trip/${trip.id}/location`,{
        driver_location: location.current.geometry
    })
    .then((response) => {
        console.log('Location broadcasted successfully', response);
    })
    .catch((error) => {
        console.error('Error broadcasting location:', error);
    });
}

const handlePassengerPickedUp = () => {
    http().post(`/api/trip/${trip.id}/start`)
    .then((response) => {
        trip.$patch(response.data);
        title.value = 'traveling to destination...';
        location.$patch({
            destination: {
                name : response.data.destination_name,
                geometry: response.data.destination
            }
        })
        trip.$patch(response.data);
    })
    .catch((error) => {
        console.error('Error starting trip:', error);
    });
}

const handleCompleteTrip = () => {
    http().post(`/api/trip/${trip.id}/end`)
    .then((response) => {
        title.value = 'trip completed!';
        trip.$patch(response.data);

        setTimeout(() => {
            trip.$reset();
            location.$reset();
            router.visit('/standby');
        }, 3000);

    })
    .catch((error) => {
        console.error('Error completing trip:', error);
    });
}

onMounted(() => {
    gMap.value.$mapPromise.then((mapObject) => {
        updateMapBounds(mapObject);

        intervalRef.value = setInterval( async () => {
            //update the driver's current position and update map bounds
            await location.updateCurrentLocation();

            //update the driver's position in the database
            broadcastDriverLocation();

            updateMapBounds(mapObject);
        }, 60000);

    });
});

onUnmounted(() => {

    clearInterval(intervalRef.value);

    intervalRef.value = null;

});


</script>
