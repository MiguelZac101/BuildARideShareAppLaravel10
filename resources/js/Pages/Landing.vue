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

        <div class="mb-6">
            <ul class="list-disc list-inside text-sm text-gray-600">
                <li>
                    Seleccione si desea ser conductor o pasajero.
                </li>
            </ul>
        </div>

        <div class="text-center">
            <div class="space-x-4">
                <PrimaryButton @click="HandleStartDriving" class="ms-4 mb-6" >
                    Empezar a conducir (Conductor)
                </PrimaryButton>
                <PrimaryButton @click="HandleFindARide" class="ms-4" >
                    Encontrar un transporte (Pasajero)
                </PrimaryButton>
            </div>
        </div>
    </AuthLayout>
</template>
