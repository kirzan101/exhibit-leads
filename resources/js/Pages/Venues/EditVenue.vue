<template>
    <b-container fluid>
        <Head>
            <title>Venues | Edit</title>
        </Head>
        <h5>
            Edit Venue |
            <Link class="btn btn-secondary" href="/venues">Back</Link>
        </h5>
        <span class="note">Note: Fields that has "*" is required</span>
        <div class="p-1">
            <b-form @submit.prent="submit">
                <b-row>
                    <b-col sm="6">
                        <b-form-group
                            label="Name"
                            label-for="name"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="name"
                                v-model="form.name"
                                :state="errors.name ? false : null"
                                required
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.name ? false : null"
                            >
                                {{ errors.name }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="6">
                        <b-form-group
                            label="Code"
                            label-for="code"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="code"
                                v-model="form.code"
                                :state="errors.code"
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.code ? false : null"
                            >
                                {{ errors.code }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="12">
                        <b-form-group
                            label="Description"
                            label-for="description"
                        >
                            <b-form-textarea
                                id="textarea-state"
                                v-model="form.description"
                                :state="errors.description ? false : null"
                                placeholder="Enter description"
                                rows="3"
                            ></b-form-textarea>
                            <b-form-invalid-feedback
                                :state="errors.description ? false : null"
                            >
                                {{ errors.description }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                </b-row>

                <hr style="width: 50%" />
                <div class="text-center">
                    <!-- <b-button type="submit" variant="success">Submit</b-button> -->
                    <b-button v-b-modal.confirm-submit-modal variant="success"
                        >Submit</b-button
                    >
                    <b-modal
                        id="confirm-submit-modal"
                        size="sm"
                        title="Notice"
                        centered
                        >Confirm submit?
                        <template #modal-footer>
                            <!-- <button modal-cancel class="btn btn-danger btn-sm m-1">Cancel</button> -->
                            <!-- <button v-b-modal.modal-close_visit class="btn btn-success btn-sm m-1" type="submit">Submit</button> -->
                            <b-button
                                variant="danger"
                                type="button"
                                @click="$bvModal.hide('confirm-submit-modal')"
                                >Close</b-button
                            >
                            <b-button
                                variant="success"
                                type="button"
                                @click="submit()"
                                >Submit</b-button
                            >
                        </template>
                    </b-modal>
                </div>
            </b-form>
        </div>
    </b-container>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
export default {
    components: {
        Link,
    },
    props: {
        errors: Object,
        venue: Object,
    },
    data() {
        return {
            form: {
                name: this.venue.name,
                code: this.venue.code,
                description: this.venue.description,
                id: this.venue.id
            },
        };
    },
    methods: {
        submit() {
            this.$bvModal.hide("confirm-submit-modal");

            // router.post("/venues", this.form);
            router.post(`/venues/${this.venue.id}`, {
                _method: "PUT",
                forceFormData: true,
                ...this.form,
            });
        },
    },
};
</script>
