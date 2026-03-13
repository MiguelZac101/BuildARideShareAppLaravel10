<script setup>

    import PrimaryButton from '@/Components/PrimaryButton.vue';

    import { Head, router} from '@inertiajs/vue3';
    import AuthLayout from '@/Layouts/AuthLayout.vue';
    import http from '@/helpers/http';

    const HandleStartDriving = () => {
        http().get('/api/driver')
            .then((response) => {
                if(response.data.driver) {
                    router.visit('/standby');
                } else {
                    router.visit('/driver');
                }
            })
            .catch((error) => {
                console.error(error)
            });
    }

    const HandleFindARide = () => {
        router.visit('/location');
    }

</script>

<template>
    <AuthLayout>
        <Head title="Welcome to BuildARideShareApp" />

        <div class="text-center">
            <div class="space-x-4">
                <PrimaryButton @click="HandleStartDriving" class="ms-4" >
                    Start Driving
                </PrimaryButton>
                <PrimaryButton @click="HandleFindARide" class="ms-4" >
                    Find a Ride
                </PrimaryButton>
            </div>
        </div>
    </AuthLayout>
</template>
