<template>
    <b-modal id="scan-upload-modal" title="Upload Image">
        <b-container fluid>
            <h5>Image</h5>
            <div class="p-1">
                <form @submit.prevent="submit">
                    <b-form-file
                        v-model="form.uploaded_image"
                        placeholder="Choose a image or drop it here..."
                        drop-placeh-older="Drop image here..."
                        accept="image/*"
                    ></b-form-file>
                    <div class="text-center mt-4">
                        <b-button variant="primary" type="submit"
                            >Submit</b-button
                        >
                    </div>
                </form>
            </div>
        </b-container>
    </b-modal>
</template>

<script>
import { useForm } from "@inertiajs/vue2";

export default {
    data() {
        return {
            form: useForm({
                uploaded_image: null,
            }),
        };
    },
    methods: {
        submit() {
            this.form.post("/scan/upload");
        },
    },
};
</script>
