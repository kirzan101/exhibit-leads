<template>
    <b-container fluid>
        <Head>
            <title>Employees | Edit</title>
        </Head>
        <h5>
            Edit Employee |
            <Link class="btn btn-secondary" href="/employees">Back</Link>
        </h5>
        <span class="note">Note: Fields that has "*" is required</span>
        <div class="p-1">
            <b-form @submit.prent="submit">
                <b-row>
                    <b-col sm="4">
                        <b-form-group
                            label="First Name"
                            label-for="first-name"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="first-name"
                                v-model="form.first_name"
                                :state="errors.first_name ? false : null"
                                required
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.first_name ? false : null"
                            >
                                {{ errors.first_name }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="4">
                        <b-form-group
                            label="Middle Name"
                            label-for="middle-name"
                        >
                            <b-form-input
                                type="text"
                                id="middle-name"
                                v-model="form.middle_name"
                                :state="errors.middle_name"
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.middle_name ? false : null"
                            >
                                {{ errors.middle_name }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="4">
                        <b-form-group
                            label="Last Name"
                            label-for="last-name"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="last-name"
                                v-model="form.last_name"
                                :state="errors.last_name ? false : null"
                                required
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.last_name ? false : null"
                            >
                                {{ errors.last_name }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="3">
                        <b-form-group
                            label="Email"
                            label-for="email"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="email"
                                v-model="form.email"
                                :state="errors.email ? false : null"
                                required
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.email ? false : null"
                                >{{ errors.email }}</b-form-invalid-feedback
                            >
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-form-group
                            label="Property"
                            label-for="property"
                            label-class="required"
                        >
                            <b-form-select
                                id="property-location"
                                v-model="form.property"
                                :state="errors.property ? false : null"
                                :options="property_locations"
                                required
                            ></b-form-select>
                            <b-form-invalid-feedback
                                :state="errors.property ? false : null"
                            >
                                {{ errors.property }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-form-group
                            label="Position"
                            label-for="position"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="position"
                                v-model="form.position"
                                :state="errors.position ? false : null"
                                required
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.position ? false : null"
                            >
                                {{ errors.position }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-form-group
                            label="User group"
                            label-for="user-group"
                            label-class="required"
                        >
                            <b-form-select
                                id="user-group"
                                v-model="form.user_group_id"
                                :state="errors.user_group_id ? false : null"
                                :options="user_groups"
                                value-field="id"
                                text-field="name"
                                required
                            >
                                <template #first>
                                    <b-form-select-option value="null" disabled
                                        >-- select --</b-form-select-option
                                    >
                                </template>
                            </b-form-select>
                            <b-form-invalid-feedback
                                :state="errors.user_group_id ? false : null"
                            >
                                {{ errors.user_group_id }}
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
        employee: Object,
        user: Object,
        user_groups: Array,
    },
    data() {
        return {
            form: {
                first_name: this.employee.first_name,
                middle_name: this.employee.middle_name,
                last_name: this.employee.last_name,
                position: this.employee.position,
                property: this.employee.property,
                email: this.user.email,
                user_id: this.user.id,
                user_group_id: this.employee.user_group_id
            },
            property_locations: [
                { text: "-- select --", value: null },
                "Astoria Plaza",
                "Astoria Current",
                "Astoria Greenbelt",
                "Astoria Palawan",
                "Astoria Boracay",
                "Astoria Bohol",
                "Stellar Potter's Ridge",
            ],
        };
    },
    methods: {
        submit() {
            this.$bvModal.hide("confirm-submit-modal");

            // router.post("/employees", this.form);

            router.post(`/employees/${this.employee.id}`, {
                _method: "PUT",
                ...this.form
            });
        },
    },
};
</script>
