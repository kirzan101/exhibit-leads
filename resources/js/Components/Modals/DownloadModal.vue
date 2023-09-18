<template>
    <b-modal title="Notice:" id="download-modal">
        <h5>{{ message }}</h5>
        <template #modal-footer>
            <b-button
                variant="danger"
                type="button"
                @click="$bvModal.hide('download-modal')"
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
        message: String
    },
    methods: {
        submit() {
            return this.$emit("submit-download");
        }
    }
}
</script>
