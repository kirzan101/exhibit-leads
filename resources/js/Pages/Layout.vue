<template>
    <div>
        <Navbar />
        <transition name="page" mode="out-in" appear>
            <main :key="$page.url" class="m-2 mt-4">
                <slot />
            </main>
        </transition>
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