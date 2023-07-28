<template>
    <b-container fluid>
        <Head>
            <title>Employees | Add</title>
        </Head>
        <h5>
            Add Employee |
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
                    <b-col sm="4">
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
                    <b-col sm="4">
                        <b-form-group
                            label="Password"
                            label-for="password"
                            label-class="required"
                        >
                            <b-form-input
                                type="password"
                                id="password"
                                v-model="form.password"
                                :state="errors.password ? false : null"
                                required
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.password ? false : null"
                                >{{ errors.password }}</b-form-invalid-feedback
                            >
                        </b-form-group>
                    </b-col>
                    <b-col sm="4">
                        <b-form-group
                            label="Property"
                            label-for="property"
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
                    <b-col sm="6">
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
                    <b-col sm="6">
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
                <b-row>
                    <b-col sm="12">
                        <b-form-group>
                            <template #label>
                                <b class="required">Choose your venues:</b
                                ><br />
                                <b-form-checkbox
                                    v-model="allSelected"
                                    aria-describedby="venues"
                                    aria-controls="venues"
                                    @change="toggleAll"
                                >
                                    {{
                                        allSelected
                                            ? "Un-select All"
                                            : "Select All"
                                    }}
                                </b-form-checkbox>
                            </template>

                            <div class="ml-3">
                                <b-form-checkbox-group
                                    v-model="selected"
                                    :options="venue_options"
                                    name="flavour-1a"
                                ></b-form-checkbox-group>
                            </div>
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
        user_groups: Array,
        venues: Array,
        properties: Array,
    },
    data() {
        return {
            form: {
                first_name: null,
                middle_name: null,
                last_name: null,
                position: null,
                property_id: null,
                email: null,
                password: null,
                user_group_id: null,
                venue_ids: null,
            },
            property_locations: [
                { text: "-- select --", value: null },
                ...this.properties.map((i) => {
                    return { text: i.name, value: i.id };
                }),
            ],
            selected: [],
            allSelected: false,
            venue_options: [
                ...this.venues.map((i) => {
                    return { text: i.name, value: i.id, disabled: false };
                }),
            ],
        };
    },
    methods: {
        submit() {
            this.$bvModal.hide("confirm-submit-modal");

            if (this.selected.length > 0) {
                this.form.venue_ids = this.selected;
            }

            router.post("/employees", this.form);
        },
        toggleAll(checked) {
            this.selected = checked
                ? this.venues
                      .map((i) => {
                          return i.id;
                      })
                      .slice()
                : [];
        },
    },
    watch: {
        selected(newValue, oldValue) {
            // Handle changes in individual flavour checkboxes
            if (newValue.length === 0) {
                this.allSelected = false;
            } else if (newValue.length === this.venues.length) {
                this.allSelected = true;
            } else {
                this.allSelected = false;
            }
        },
    },
};
</script>
