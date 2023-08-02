<template>
    <b-container fluid>
        <Head>
            <title>Sources | Show</title>
        </Head>
        <h5>
            Show Source |
            <Link class="btn btn-secondary" href="/sources">Back</Link>
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
                                readonly
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.name ? false : null"
                            >
                                {{ errors.name }}
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
                                readonly
                            ></b-form-textarea>
                            <b-form-invalid-feedback
                                :state="errors.description ? false : null"
                            >
                                {{ errors.description }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                </b-row>
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
        source: Array,
    },
    data() {
        return {
            form: {
                name: this.source.name,
                description: this.source.description,
            },
        };
    },
};
</script>
