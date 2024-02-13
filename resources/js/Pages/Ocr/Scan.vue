<template>
    <b-container fluid>
        <Head>
            <title>Leads | Scan</title>
        </Head>
        <h5>Scan image</h5>
        <div class="p-1">
            <form @submit.prevent="submit">
                <!-- <input
                    type="file"
                    @input="form.uploaded_image = $event.target.files[0]"
                /> -->
                <b-form-file
                    v-model="form.uploaded_image"
                    placeholder="Choose a file or drop it here..."
                    drop-placeholder="Drop file here..."
                    accept="image/*"
                ></b-form-file>
                <!-- <progress
                    v-if="form.progress"
                    :value="form.progress.percentage"
                    max="100"
                >
                    {{ form.progress.percentage }}%
                </progress> -->
                <div class="text-center mt-4">
                    <!-- <button type="submit">Submit</button> -->
                    <b-button variant="primary" type="submit">Submit</b-button>
                </div>
            </form>
        </div>
    </b-container>
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
