<script setup>

    import PrimaryButton from '@/Components/PrimaryButton.vue';

    import { Head, router} from '@inertiajs/vue3';
    import AuthLayout from '@/Layouts/AuthLayout.vue';

    const HandleFindARide = () => {
        router.visit('/location');
    }

</script>

<template>
    <AuthLayout>
        <Head title="Welcome to BuildARideShareApp" />

        <div class="text-center">
            <div class="space-x-4">
                <PrimaryButton class="ms-4" href="/login">
                    Start Driving
                </PrimaryButton>
                <PrimaryButton @click="HandleFindARide" class="ms-4" href="/login">
                    Find a Ride
                </PrimaryButton>
            </div>
        </div>
    </AuthLayout>
</template>
