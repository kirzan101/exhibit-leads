<template>
    <b-container fluid>
        <Head>
            <title>Profile</title>
        </Head>
        <!--Alert message here start-->
        <b-alert
            v-if="$page.props.flash.success"
            variant="success"
            dismissible
            fade
            :show="showAlert($page.props.flash.success)"
            @dismissed="clearNotif"
        >
            {{ $page.props.flash.success }}
        </b-alert>
        <b-alert
            v-if="$page.props.flash.error"
            variant="danger"
            dismissible
            fade
            :show="showAlert($page.props.flash.error)"
            @dismissed="clearNotif"
        >
            {{ $page.props.flash.error }}
        </b-alert>
        <!--Alert message here end-->

        <h5>Edit Profile</h5>
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
                        <b-form-group label="Password" label-for="password">
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
                    <b-col sm="3">
                        <b-form-group
                            label="Property"
                            label-for="property"
                            label-class="required"
                        >
                            <b-form-select
                                id="property-location"
                                v-model="form.property_id"
                                :state="errors.property_id ? false : null"
                                :options="property_options"
                                required
                            ></b-form-select>
                            <b-form-invalid-feedback
                                :state="errors.property_id ? false : null"
                            >
                                {{ errors.property_id }}
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
                </b-row>

                <b-row>
                    <b-col sm="4">
                        <b-form-group label="Is Active" label-for="is-active">
                            <b-form-checkbox
                                id="is-active"
                                v-model="form.is_active"
                                name="is-active"
                                :value="true"
                            >
                            </b-form-checkbox>
                            <b-form-invalid-feedback
                                :state="errors.is_active ? false : null"
                            >
                                {{ errors.is_active }}
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
        properties: Array,
    },
    data() {
        return {
            form: {
                first_name: this.employee.first_name,
                middle_name: this.employee.middle_name,
                last_name: this.employee.last_name,
                position: this.employee.position,
                property_id: this.employee.property_id,
                email: this.user.email,
                password: null,
                is_active: this.user.is_active,
                exhibitor_id: this.employee.exhibitor_id
            },
            property_options: [
                { value: null, text: "-- select --" },
                ...this.properties.map((item) => {
                    return { value: item.id, text: item.name };
                }),
            ],
            alert: false,
        };
    },
    methods: {
        submit() {
            this.$bvModal.hide("confirm-submit-modal");

            router.post("/profile/edit", {
                _method: "PUT",
                ...this.form,
            });
        },
        showAlert(data) {
            this.alert = data ? true : false;

            return this.alert;
        },
        clearNotif() {
            this.$page.props.flash.success = null;
            this.$page.props.flash.error = null;
            this.$page.props.flash.message = null;
        },
    },
};
</script>
