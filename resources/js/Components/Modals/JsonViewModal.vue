<template>
    <b-modal id="modal-json" :title="title">
        <pre>{{ prettier }}</pre>
        <template #modal-footer>
            <b-button
                variant="danger"
                type="button"
                @click="$bvModal.hide('modal-json')"
                >Close</b-button
            >
        </template>
    </b-modal>
</template>

<script>
export default {
    props: {
        jsonData: String,
        title: String,
    },
    computed: {
        prettier() {
            return JSON.stringify(JSON.parse(this.jsonData), null, 2);
        },
    },
};
</script>
