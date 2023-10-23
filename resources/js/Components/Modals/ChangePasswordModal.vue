<template>
    <b-modal
        ref="change-password-modal"
        centered
        no-close-on-esc
        no-close-on-backdrop
        hide-header-close
        title="Notice:"
    >
        <form @submit.prevent="submit">
            <b-form-group
                label="Enter new password"
                label-for="password-input"
                class="mb-0"
            >
                <b-form-input
                    v-model="password"
                    type="password"
                    placeholder="Password"
                    id="password-input"
                    min="3"
                ></b-form-input>
            </b-form-group>
        </form>

        <template #modal-footer>
            <b-button
                variant="info"
                type="button"
                v-if="password.length > 3"
                @click="submit()"
                >Save</b-button
            >
            <b-button variant="info" type="button" :disabled="true" v-else
                >Save</b-button
            >
        </template>
    </b-modal>
</template>

<script>
import { router } from "@inertiajs/vue2";

export default {
    props: {
        user_id: Number,
    },
    data() {
        return {
            password: "",
        };
    },
    methods: {
        submit() {
            const form = {
                // venue_ids: this.current_venues, //for checkbox
                password: this.password,
            };

            // return this.$emit("submit-change-password", this.password);
            router.post(`/employees/${this.user_id}/password-update`, {
                _method: "PUT",
                ...form,
            });

            // hide modal
            this.$refs["change-password-modal"].hide();
        },
        showChangePasswordModal() {
            this.$refs["change-password-modal"].show();
        },
    },
    mounted() {
        this.showChangePasswordModal();
    },
};
</script>
