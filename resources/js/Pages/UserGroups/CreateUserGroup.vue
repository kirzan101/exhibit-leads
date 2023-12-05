<template>
    <b-container fluid>
        <Head>
            <title>User Groups | Create</title>
        </Head>
        <h5>
            Create User Group |
            <Link class="btn btn-secondary" href="/usergroups">Back</Link>
        </h5>
        <span class="note">Note: Fields that has "*" is required</span>

        <div class="p-1">
            <b-form @submit.prevent="submit">
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
                            label="Department"
                            label-for="department"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="department"
                                v-model="form.department"
                                :state="errors.department"
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.department ? false : null"
                            >
                                {{ errors.department }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="6">
                        <b-form-group
                            label="Code"
                            label-for="code"
                            label-class="required"
                        >
                            <b-form-select
                                id="code"
                                v-model="form.code"
                                :state="errors.code ? false : null"
                                :options="code_options"
                                required
                            ></b-form-select>
                            <b-form-invalid-feedback
                                :state="errors.code ? false : null"
                            >
                                {{ errors.code }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="6">
                        <b-form-group
                            label="Property Location"
                            label-for="property-location"
                            label-class="required"
                        >
                            <b-form-select
                                id="property-location"
                                v-model="form.property_id"
                                :state="errors.property_id ? false : null"
                                :options="property_locations"
                                required
                            ></b-form-select>
                            <b-form-invalid-feedback
                                :state="errors.property_id ? false : null"
                            >
                                {{ errors.property_id }}
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
                                id="description"
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

                <br />
                <h5>Permissions</h5>

                <UserGroupPermissionTable
                    :permissions="permissions"
                    :modules="modules"
                    :disabled="disabled"
                    @select-permission="receivedSelected"
                />

                <hr style="width: 50%" />
                <div class="text-center">
                    <b-button v-b-modal.confirm-submit-modal variant="success"
                        >Submit</b-button
                    >
                    <SubmitModal
                        message="Confirm submit?"
                        @submit-confirm="submit"
                    />
                </div>
            </b-form>
        </div>
    </b-container>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import UserGroupPermissionTable from "../../Components/UserGroupPermissionTable.vue";
import SubmitModal from "../../Components/Modals/SubmitModal.vue";

export default {
    components: {
        Link,
        UserGroupPermissionTable,
        SubmitModal,
    },
    props: {
        errors: Object,
        properties: Array,
        permissions: Array,
        modules: Array,
        codes: Array,
    },
    data() {
        return {
            disabled: false,
            form: {
                name: null,
                department: null,
                description: null,
                property_id: null,
                code: null,
                permissions: [],
            },
            property_locations: [
                { text: "-- select --", value: null },
                ...this.properties.map((i) => {
                    return { text: i.name, value: i.id };
                }),
            ],
            code_options: [
                { text: "-- select --", value: null },
                ...this.codes.map((i) => {
                    return { text: i, value: i };
                }),
            ],
            selectedPermissions: [],
        };
    },
    methods: {
        submit() {
            this.$bvModal.hide("confirm-submit-modal");

            this.form.permissions = this.selectedPermissions.sort();

            // console.log(this.form);

            router.post("/usergroups", {
                forceFormData: true,
                ...this.form,
            });
        },
        // get the data from props of UserGroupPermission component
        receivedSelected(data) {
            this.selectedPermissions = data;
        },
    },
};
</script>
