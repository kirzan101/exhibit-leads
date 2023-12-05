<template>
    <b-modal title="Notice:" id="confirm-submit-modal" size="sm" centered>
        <h5>{{ message }}</h5>
        <template #modal-footer>
            <b-button
                variant="danger"
                type="button"
                @click="$bvModal.hide('confirm-submit-modal')"
                >Close</b-button
            >
            <b-button variant="success" type="button" @click="submit()"
                >Submit</b-button
            >
        </template>
    </b-modal>
</template>

<script>
export default {
    props: {
        message: String,
    },
    methods: {
        submit() {
            return this.$emit("submit-confirm");
        },
    },
};
</script>
