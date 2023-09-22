<template>
    <b-modal id="image-modal" title="Uploaded Image">
        <form @submit.prevent="submit">
            <div>
                <b-img v-if="url" :src="url" fluid alt="uploaded"></b-img>
            </div>
        </form>

        <template #modal-footer>
            <b-button
                variant="danger"
                type="button"
                @click="$bvModal.hide('image-modal')"
                >Close</b-button
            >
        </template>
    </b-modal>
</template>

<script>
export default {
    props: {
        url: String,
    },
};
</script>
