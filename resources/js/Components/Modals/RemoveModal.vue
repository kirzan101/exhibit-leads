<template>
    <b-modal title="Notice:" id="remove-modal">
        <h5>Remove Assigned?</h5>
        <template #modal-footer>
            <b-button
                variant="danger"
                type="button"
                @click="$bvModal.hide('remove-modal')"
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
    methods: {
        submit() {
            return this.$emit("submit-remove");
        }
    }
}
</script>
