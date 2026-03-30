<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4 text-center">{{ title }}</h1>
        <div>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap :zoom="14" :center="location.current.geometry" ref="gMap"
                            style="width:100%; height: 256px;">
                            <GMapMarker :position="location.current.geometry" :icon="currentIcon" />
                            <GMapMarker :position="trip.driver_location" :icon="driverIcon" />
                        </GMapMap>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <span>{{ message }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useLocationStore } from '@/stores/location';
import { useTripStore } from '@/stores/trip';

import { onMounted, ref } from 'vue';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import { router } from '@inertiajs/vue3'


const location = useLocationStore();
const trip = useTripStore();

const title = ref('Waiting for ride request...')
const message = ref('when the driver accept the trip, their info will appers here')

const gMap = ref(null)

const currentIcon = {
    url: 'https://openmoji.org/data/color/svg/1F920.svg',
    scaledSize: {
        width: 24,
        height: 24
    }
}

const driverIcon = {
    url: 'https://openmoji.org/data/color/svg/1F698.svg',
    scaledSize: {
        width: 32,
        height: 32
    }
}

onMounted( () => {

    let echo = new Echo({
        broadcaster: 'pusher',
        key: 'local',
        cluster: 'mt1',
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss']
    })


    console.log('Listening for trip accepted events on channel: passenger_' + trip.user_id);

    echo.channel(`passenger_${trip.user_id}`)
        .listen('TripAccepted', (e) => {
            trip.$patch(e.trip);

            console.log('TripAccepted', e)

            title.value = "A driver is on the way!"
            message.value = `${e.trip.driver.user.name} is coming in a ${e.trip.driver.year} ${e.trip.driver.color} ${e.trip.driver.make} ${e.trip.driver.model} with a license plate #${e.trip.driver.license_plate}`

        })
        .listen('TripLocationUpdated', (e) => {
            trip.$patch(e.trip);

            console.log('TripLocationUpdated', e)

        })
        .listen('TripStarted', (e) => {
            trip.$patch(e.trip);
            location.$patch({
                current: {
                    geometry: e.trip.destination
                }
            })

            console.log('TripStarted', e)

            title.value = "Enjoy your ride!"
            message.value = `You are on the way to ${e.trip.destination_name}`

        })
        .listen('TripEnded', (e) => {
            trip.$patch(e.trip);

            title.value = "Trip completed!"
            message.value = `You have arrived at your destination: ${e.trip.destination_name}`

            setTimeout(() => {
                trip.$reset();
                location.$reset();
                title.value = 'Waiting for ride request...'
                message.value = 'when the driver accept the trip, their info will appers here'

                router.visit('/landing');
            }, 5000)
        })
})

</script>
