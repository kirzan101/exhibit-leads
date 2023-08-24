<template>
    <b-modal id="done-modal" title="Notice">
        <p>Mark as Done?</p>
        <template #modal-footer>
            <b-button
                variant="danger"
                type="button"
                @click="$bvModal.hide('done-modal')"
                >Close</b-button
            >
            <b-button variant="success" type="button" @click="submit"
                >Yes</b-button
            >
        </template>
    </b-modal>
</template>

<script>
import { router } from "@inertiajs/vue2";

export default {
    props: {
        status: Boolean,
        lead_id: Number,
        employee_type: String
    },
    data() {
        return {
            form: {
                status: this.status,
                lead_id: this.lead_id,
                employee_type: this.employee_type
            }
        };
    },
    methods: {
        submit() {
            router.post("/done", this.form);
            this.$bvModal.hide("done-modal");
        },
    },
};
</script>
