<template>
    <div class="p-1">
        <b-form @submit.prevent="submit">
            <b-row>
                <b-col sm="3">
                    <b-form-group
                        label="Title"
                        label-for="title"
                        label-class="required"
                    >
                        <b-form-select
                            id="title"
                            :options="titles"
                            v-model="form.title"
                            :state="errors.title ? false : null"
                            :disabled="is_disabled"
                        ></b-form-select>
                        <b-form-invalid-feedback
                            :state="errors.title ? false : null"
                        >
                            {{ errors.title }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
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
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.first_name ? false : null"
                        >
                            {{ errors.first_name }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group label="Middle Name" label-for="middle-name">
                        <b-form-input
                            type="text"
                            id="middle-name"
                            v-model="form.middle_name"
                            :disabled="is_disabled"
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
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
                            :disabled="is_disabled"
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
                        label="Spouse/Partner First Name"
                        label-for="spouse-first-name"
                    >
                        <b-form-input
                            type="text"
                            id="spouse-first-name"
                            v-model="form.spouse_first_name"
                            :state="errors.spouse_first_name ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.spouse_first_name ? false : null"
                        >
                            {{ errors.spouse_first_name }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group
                        label="Spouse/Partner Last Name"
                        label-for="spouse-last-name"
                    >
                        <b-form-input
                            type="text"
                            id="spouse-last-name"
                            v-model="form.spouse_last_name"
                            :state="errors.spouse_last_name ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.spouse_last_name ? false : null"
                        >
                            {{ errors.spouse_last_name }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group
                        label="Age"
                        label-for="age"
                        label-class="required"
                    >
                        <b-form-input
                            type="number"
                            id="age"
                            min="0"
                            v-model="form.age"
                            :state="errors.age ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.age ? false : null"
                        >
                            {{ errors.age }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group label="Gender" label-for="gender">
                        <b-form-select
                            id="gender"
                            v-model="form.gender"
                            :state="errors.gender ? false : null"
                            :options="genders"
                            :disabled="is_disabled"
                        ></b-form-select>
                        <b-form-invalid-feedback
                            :state="errors.gender ? false : null"
                        >
                            {{ errors.gender }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="12">
                    <b-form-group
                        label="Address"
                        label-for="address"
                        label-class="required"
                    >
                        <b-form-input
                            type="text"
                            id="address"
                            v-model="form.address"
                            :state="errors.address ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.address ? false : null"
                        >
                            {{ errors.address }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="12">
                    <b-form-group
                        label="Secondary Address"
                        label-for="secondary-address"
                    >
                        <b-form-input
                            type="text"
                            id="secondary-address"
                            v-model="form.secondary_address"
                            :disabled="is_disabled"
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="3">
                    <b-form-group label="Nationality" label-for="nationality">
                        <b-form-input
                            type="text"
                            id="nationality"
                            v-model="form.nationality"
                            :state="errors.nationality ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.nationality ? false : null"
                        >
                            {{ errors.nationality }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group label="Civil Status" label-for="civil-status">
                        <b-form-select
                            id="civil-status"
                            v-model="form.civil_status"
                            :state="errors.civil_status ? false : null"
                            :options="civil_statuses"
                            :disabled="is_disabled"
                        ></b-form-select>
                        <b-form-invalid-feedback
                            :state="errors.civil_status ? false : null"
                        >
                            {{ errors.civil_status }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group
                        label="Occupation"
                        label-for="occupation"
                        label-class="required"
                    >
                        <b-form-input
                            type="text"
                            id="occupation"
                            v-model="form.occupation"
                            :state="errors.occupation ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.occupation ? false : null"
                        >
                            {{ errors.occupation }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group
                        label="Spouse Occupation"
                        label-for="spouse-occupation"
                    >
                        <b-form-input
                            type="text"
                            id="spouse-occupation"
                            v-model="form.spouse_occupation"
                            :state="errors.spouse_occupation ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.spouse_occupation ? false : null"
                        >
                            {{ errors.spouse_occupation }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="4">
                    <b-form-group label="Company Name" label-for="company-name">
                        <b-form-input
                            type="text"
                            id="company-name"
                            v-model="form.company_name"
                            :state="errors.company_name ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.company_name ? false : null"
                        >
                            {{ errors.company_name }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="4">
                    <b-form-group
                        label="Company Number"
                        label-for="company-number"
                    >
                        <b-form-input
                            type="text"
                            id="company-number"
                            v-model="form.company_number"
                            :disabled="is_disabled"
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="4">
                    <b-form-group
                        label="Nature of business"
                        label-for="nature-of-business"
                    >
                        <b-form-input
                            type="text"
                            id="nature-of-business"
                            v-model="form.nature_of_business"
                            :state="errors.nature_of_business ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.nature_of_business ? false : null"
                        >
                            {{ errors.nature_of_business }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="12">
                    <b-form-group
                        label="Email"
                        label-for="email"                    >
                        <b-form-input
                            type="email"
                            id="email"
                            v-model="form.email"
                            :state="errors.email ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.email ? false : null"
                        >
                            {{ errors.email }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="4">
                    <b-form-group
                        label="Mobile Number One"
                        label-for="mobile-number-one"
                        label-class="required"
                    >
                        <b-form-input
                            type="text"
                            id="mobile-number-one"
                            v-model="form.mobile_number_one"
                            :state="errors.mobile_number_one ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.mobile_number_one ? false : null"
                        >
                            {{ errors.mobile_number_one }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="4">
                    <b-form-group
                        label="Mobile Number Two"
                        label-for="mobile-number-two"
                    >
                        <b-form-input
                            type="text"
                            id="mobile-number-two"
                            v-model="form.mobile_number_two"
                            :disabled="is_disabled"
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="4">
                    <b-form-group label="Telephone" label-for="telephone">
                        <b-form-input
                            type="text"
                            id="telephone"
                            v-model="form.telephone"
                            :disabled="is_disabled"
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="3">
                    <b-form-group
                        label="Combined monthly Income"
                        label-for="combined-monthly-income"
                    >
                        <b-form-select
                            id="combined-monthly-income"
                            v-model="form.combined_monthly_income"
                            :state="
                                errors.combined_monthly_income ? false : null
                            "
                            :options="monthly_incomes"
                            :disabled="is_disabled"
                        ></b-form-select>
                        <b-form-invalid-feedback
                            :state="
                                errors.combined_monthly_income ? false : null
                            "
                        >
                            {{ errors.combined_monthly_income }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group
                        label="Internet Connection"
                        label-for="internet-connection"
                    >
                        <b-form-select
                            id="internet-connection"
                            v-model="form.internet_connection"
                            :state="errors.internet_connection ? false : null"
                            :options="internet_connections"
                            :disabled="is_disabled"
                        ></b-form-select>
                        <b-form-invalid-feedback
                            :state="errors.internet_connection ? false : null"
                        >
                            {{ errors.internet_connection }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group
                        label="Other gadget(s) you own aside from cellphone:"
                    >
                        <b-form-checkbox-group
                            id="other-gadgets"
                            v-model="form.owned_gadgets"
                            :state="errors.owned_gadgets ? false : null"
                            :options="gadgets"
                            :disabled="is_disabled"
                        ></b-form-checkbox-group>
                        <b-form-invalid-feedback
                            :state="errors.owned_gadgets ? false : null"
                        >
                            {{ errors.owned_gadgets }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group
                        label="Other's specified"
                        label-for="others-specified"
                        v-slot="{ ariaDescribedby }"
                        v-if="
                            form.owned_gadgets != null &&
                            form.owned_gadgets.includes('Others')
                        "
                    >
                        <b-form-input
                            type="text"
                            id="others-specified"
                            v-model="form.other_gadgets"
                            :aria-describedby="ariaDescribedby"
                            :disabled="is_disabled"
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="4">
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
                            :disabled="is_disabled"
                        ></b-form-select>
                        <b-form-invalid-feedback
                            :state="errors.property_id ? false : null"
                        >
                            {{ errors.property_id }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="4">
                    <b-form-group label="Venue" label-for="venue">
                        <b-form-select
                            id="venue"
                            v-model="form.venue_id"
                            :state="errors.venue_id ? false : null"
                            :options="venues_options"
                            :disabled="is_disabled"
                        ></b-form-select>
                        <b-form-invalid-feedback
                            :state="errors.venue_id ? false : null"
                        >
                            {{ errors.venue_id }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="4">
                    <b-form-group
                        label="Contract file"
                        label-for="contract-file"
                    >
                        <b-form-file
                            id="contract-file"
                            v-model="form.contract_file"
                            :state="errors.contract_file ? false : null"
                            placeholder="Choose an image"
                            accept="image/png, image/jpg, image/jpeg"
                            :disabled="is_disabled"
                        ></b-form-file>
                        <div class="mt-3">
                            Selected file:
                            <b-button
                                class="btn btn-warning"
                                v-if="
                                    form.contract_file ||
                                    lead.uploaded_contract_file
                                "
                                v-b-modal.image-modal
                                >{{
                                    lead.uploaded_contract_file
                                        ? lead.contract_file_name
                                        : form.contract_file.name
                                }}</b-button
                            >
                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="4">
                    <b-form-group
                        label="Membership Type"
                        label-for="membership-type"
                    >
                        <b-form-input
                            type="text"
                            id="membership-type"
                            v-model="form.membership_type"
                            :state="errors.membership_type ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.membership_type ? false : null"
                        >
                            {{ errors.membership_type }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="4">
                    <b-form-group
                        label="Presentation date"
                        label-for="presentation-date"
                    >
                        <b-input-group>
                            <b-form-input
                                type="date"
                                id="presentation-date"
                                v-model="form.presentation_date"
                                :disabled="is_disabled"
                                :state="errors.presentation_date ? false : null"
                            ></b-form-input>
                            <b-form-input
                                type="time"
                                id="presentation-time"
                                v-if="form.presentation_date"
                                v-model="form.presentation_time"
                                :disabled="is_disabled"
                                :state="errors.presentation_time ? false : null"
                            ></b-form-input>
                            <b-form-input
                                type="time"
                                id="presentation-time"
                                disabled
                                v-else
                            ></b-form-input>
                        </b-input-group>
                        <b-form-invalid-feedback
                            :state="errors.presentation_date ? false : null"
                        >
                            {{ errors.presentation_date }}
                        </b-form-invalid-feedback>
                        <b-form-invalid-feedback
                            :state="errors.presentation_time ? false : null"
                        >
                            {{ errors.presentation_time }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="4">
                    <b-form-group
                        label="Source"
                        label-for="source"
                        label-class="required"
                    >
                        <b-input-group>
                            <b-form-input
                                v-model="form.source"
                                :disabled="is_disabled"
                            ></b-form-input>

                            <template #prepend>
                                <b-form-select
                                    id="source"
                                    v-model="form.source_prefix"
                                    :state="errors.source_prefix ? false : null"
                                    :options="source_options"
                                    :disabled="is_disabled"
                                ></b-form-select>
                            </template>
                        </b-input-group>
                        <b-form-invalid-feedback
                            :state="errors.source ? false : null"
                            v-if="!form.source_prefix"
                        >
                            {{ errors.source_prefix }}
                        </b-form-invalid-feedback>
                        <b-form-invalid-feedback
                            :state="errors.source ? false : null"
                            v-else
                        >
                            {{ errors.source }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="3">
                    <b-form-group label="Date Filled" label-for="date-filled">
                        <b-form-input
                            type="date"
                            id="date-filled"
                            v-model="form.date_filled"
                            :disabled="true"
                            :state="errors.date_filled ? false : null"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.date_filled ? false : null"
                        >
                            {{ errors.date_filled }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group label="Refer By" label-for="refer-by">
                        <b-form-input
                            type="text"
                            id="refer-by"
                            v-model="form.refer_by"
                            :state="errors.refer_by ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.refer_by ? false : null"
                        >
                            {{ errors.refer_by }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group
                        label="Holiday Consultant"
                        label-for="holiday-consultant"
                    >
                        <b-form-input
                            type="text"
                            id="holiday-consultant"
                            v-model="form.holiday_consultant"
                            :state="errors.holiday_consultant ? false : null"
                            :disabled="is_disabled"
                        ></b-form-input>
                        <b-form-invalid-feedback
                            :state="errors.holiday_consultant ? false : null"
                        >
                            {{ errors.holiday_consultant }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group
                        label="Is Confidential?"
                        label-for="is-confidential"
                    >
                        <b-form-checkbox
                            id="is-confidential"
                            v-model="form.is_confidential"
                            name="is-confidential"
                            :disabled="is_disabled"
                        >
                            Yes
                        </b-form-checkbox>
                    </b-form-group>
                </b-col>
            </b-row>
            <hr style="width: 50%" />
            <div class="text-center" v-if="!is_disabled">
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

        <UploadModal :url="image" />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import UploadModal from "./Modals/UploadModal.vue";

export default {
    components: {
        Link,
        UploadModal,
    },
    props: {
        errors: Object,
        lead: Object,
        properties: Array,
        venues: Array,
        sources: Array,
        is_disabled: Boolean,
        form_type: String,
    },
    data() {
        return {
            form: this.$inertia.form({
                first_name: this.lead.first_name,
                middle_name: this.lead.middle_name,
                last_name: this.lead.last_name,
                title: this.lead.title,
                alias: this.lead.alias,
                suffix: this.lead.suffix,
                age: this.lead.age,
                address: this.lead.address,
                secondary_address: this.lead.secondary_address,
                nationality: this.lead.nationality,
                gender: this.lead.gender,
                civil_status: this.lead.civil_status,
                company_name: this.lead.company_name,
                company_number: this.lead.company_number,
                occupation: this.lead.occupation,
                email: this.lead.email,
                mobile_number_one: this.lead.mobile_number_one,
                mobile_number_two: this.lead.mobile_number_two,
                telephone: this.lead.telephone,
                fax: this.lead.fax,
                combined_monthly_income: this.lead.combined_monthly_income,
                internet_connection: this.lead.internet_connection,
                owned_gadgets: this.lead.owned_gadgets,
                other_gadgets: this.lead.other_gadgets,
                spouse_occupation: this.lead.spouse_occupation,
                nature_of_business: this.lead.nature_of_business,
                property_id: this.lead.property_id,
                contract_file: this.lead.contract_file,
                presentation_date: this.lead.presentation_date,
                presentation_time: this.lead.presentation_time,
                source: this.lead.source,
                source_prefix: this.lead.source_prefix,
                membership_type: this.lead.membership_type,
                refer_by: this.lead.refer_by,
                holiday_consultant: this.lead.holiday_consultant,
                is_confidential: this.lead.is_confidential,
                venue_id: this.lead.venue_id,
                id: this.lead.id,
                spouse_first_name: this.lead.spouse_first_name,
                spouse_last_name: this.lead.spouse_last_name,
                date_filled: this.lead.date_filled,
            }),
            titles: [
                { text: "-- select --", value: null },
                "Mr.",
                "Ms.",
                "Mrs.",
                "Hon.",
                "Atty.",
                "Dr.",
                "Dra.",
            ],
            genders: [{ text: "-- select --", value: null }, "Male", "Female"],
            civil_statuses: [
                { text: "-- select --", value: null },
                "Single",
                "Married",
                "With Live-in partner",
                "Widowed/Separated",
            ],
            monthly_incomes: [
                { text: "-- select --", value: null },
                "Php 40k Below",
                "Php 40k - 49,999",
                "Php 50k Above",
            ],
            gadgets: [
                { text: "Desktop", value: "Desktop" },
                { text: "Laptop", value: "Laptop" },
                { text: "Tablet", value: "Tablet" },
                { text: "Others", value: "Others" },
            ],
            internet_connections: [
                { text: "-- select --", value: null },
                "Postpaid",
                "Prepaid",
            ],
            property_locations: [
                { text: "-- select --", value: null },
                ...this.properties.map((i) => {
                    return { text: i.name, value: i.id };
                }),
            ],
            venues_options: [
                { text: "-- select --", value: null },
                ...this.venues.map((i) => {
                    return { text: i.name, value: i.id };
                }),
            ],
            source_options: [
                { text: "-- select --", value: null },
                ...this.sources.map((i) => {
                    return { text: i.name, value: i.name };
                }),
            ],
        };
    },
    methods: {
        submit() {
            this.$bvModal.hide("confirm-submit-modal");

            router.post(`/${this.form_type}/${this.lead.id}`, {
                _method: "PUT",
                forceFormData: true,
                ...this.form,
            });
        },
    },
    computed: {
        image() {
            if (this.form.contract_file) {
                return URL.createObjectURL(this.form.contract_file);
            }

            return this.lead.uploaded_contract_file;
        },
    },
};
</script>
