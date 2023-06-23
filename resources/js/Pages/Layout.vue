<template>
    <div>
        <Navbar />
        <div class="m-2 mt-4">
            <slot />
        </div>
    </div>
</template>

<script>
    import Navbar from '../Components/Navbar.vue';
    export default {
        components: {
            Navbar
        }
    }
</script>