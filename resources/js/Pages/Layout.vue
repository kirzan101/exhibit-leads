<template>
    <div>
        <Navbar />
        <transition name="page" mode="out-in" appear>
            <main :key="$page.url" class="m-2 mt-4">
                <slot />
            </main>
        </transition>
        <ChangePasswordModal v-if="!is_password_changed" :user_id="user_id" />
    </div>
</template>

<script>
import Navbar from '../Components/Navbar.vue';
import ChangePasswordModal from '../Components/Modals/ChangePasswordModal.vue';
export default {
    data() {
        return {
            user_id: this.$page.props.auth.user.id,
            is_password_changed: this.$page.props.auth.user.is_password_changed,
            is_active: this.$page.props.auth.user.is_active,
        }
    },
    components: {
        Navbar,
        ChangePasswordModal
    },
}
</script>
