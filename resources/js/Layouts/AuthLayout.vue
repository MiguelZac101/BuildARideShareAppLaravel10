<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

import { onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

//toda la validacion de autenticacion se hace en el backend, asi que solo verificamos si el token existe para redirigir al login

//onMounted es hook de Vue que corre después de montar el componente en DOM, es decir, después de que el componente se renderiza por primera vez.
onMounted(() => {
    if (!localStorage.getItem('token')) {//valida que el token no exista en localStorage, lo que indicaría que el usuario no ha iniciado sesión o su sesión ha expirado.
        router.visit('/login');
    }
});

const checkTokenAuthenticity = () => {
    // Realiza una solicitud al backend para verificar el token
    axios.get('/api/user', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
        }
    })
    .then(response => {
        // Si la respuesta es exitosa, el token es válido, no hacemos nada
    })
    .catch(error => {
        // Si hay un error (por ejemplo, token inválido), eliminamos el token y redirigimos al login
        localStorage.removeItem('token');
        router.visit('/login');
    });
};

checkTokenAuthenticity();

</script>
<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <Link href="/">
            <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
