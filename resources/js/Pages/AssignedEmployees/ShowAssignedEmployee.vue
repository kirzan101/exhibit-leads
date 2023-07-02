<template>
    <b-container fluid>
        <h5>
            <div class="row">
                <div class="col-sm-6">
                    Member |
                    <Link href="/assigned-employees" class="btn btn-success"
                        >Back</Link
                    >
                </div>
                <div class="col-sm-6 align-right">Assigned to: {{ assigned_employee }}</div>
            </div>
        </h5>
        <div class="p-1">
            <b-form>
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
                                :disabled="true"
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
                                readonly
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.first_name ? false : null"
                            >
                                {{ errors.first_name }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-form-group
                            label="Middle Name"
                            label-for="middle-name"
                        >
                            <b-form-input
                                type="text"
                                id="middle-name"
                                v-model="form.middle_name"
                                readonly
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
                                readonly
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
                        <b-form-group label="Alias" label-for="alias">
                            <b-form-input
                                type="text"
                                id="alias"
                                v-model="form.alias"
                                readonly
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-form-group label="Suffix" label-for="suffix">
                            <b-form-input
                                type="text"
                                id="suffix"
                                v-model="form.suffix"
                                readonly
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-form-group
                            label="Birth date"
                            label-for="birth-date"
                            label-class="required"
                        >
                            <b-form-input
                                type="date"
                                id="birth-date"
                                v-model="form.birth_date"
                                :state="errors.birth_date ? false : null"
                                readonly
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.birth_date ? false : null"
                            >
                                {{ errors.birth_date }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-form-group
                            label="Gender"
                            label-for="gender"
                            label-class="required"
                        >
                            <b-form-select
                                id="gender"
                                v-model="form.gender"
                                :state="errors.gender ? false : null"
                                :options="genders"
                                :disabled="true"
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
                                readonly
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
                                readonly
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="3">
                        <b-form-group
                            label="Nationality"
                            label-for="nationality"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="nationality"
                                v-model="form.nationality"
                                :state="errors.nationality ? false : null"
                                readonly
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.nationality ? false : null"
                            >
                                {{ errors.nationality }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-form-group
                            label="Civil Status"
                            label-for="civil-status"
                            label-class="required"
                        >
                            <b-form-select
                                id="civil-status"
                                v-model="form.civil_status"
                                :state="errors.civil_status ? false : null"
                                :options="civil_statuses"
                                :disabled="true"
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
                                readonly
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
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="spouse-occupation"
                                v-model="form.spouse_occupation"
                                :state="errors.spouse_occupation ? false : null"
                                readonly
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
                        <b-form-group
                            label="Company Name"
                            label-for="company-name"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="company-name"
                                v-model="form.company_name"
                                :state="errors.company_name ? false : null"
                                readonly
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
                                readonly
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col sm="4">
                        <b-form-group
                            label="Nature of business"
                            label-for="nature-of-business"
                            label-class="required"
                        >
                            <b-form-input
                                type="text"
                                id="nature-of-business"
                                v-model="form.nature_of_business"
                                :state="
                                    errors.nature_of_business ? false : null
                                "
                                readonly
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="
                                    errors.nature_of_business ? false : null
                                "
                            >
                                {{ errors.nature_of_business }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="6">
                        <b-form-group
                            label="Email"
                            label-for="email"
                            label-class="required"
                        >
                            <b-form-input
                                type="email"
                                id="email"
                                v-model="form.email"
                                :state="errors.email ? false : null"
                                readonly
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.email ? false : null"
                            >
                                {{ errors.email }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="6">
                        <b-form-group label="Fax" label-for="fax">
                            <b-form-input
                                type="text"
                                id="fax"
                                v-model="form.fax"
                                readonly
                            ></b-form-input>
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
                                readonly
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
                                readonly
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col sm="4">
                        <b-form-group label="Telephone" label-for="telephone">
                            <b-form-input
                                type="text"
                                id="telephone"
                                v-model="form.telephone"
                                readonly
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="3">
                        <b-form-group
                            label="Combined monthly Income"
                            label-for="combined-monthly-income"
                            label-class="required"
                        >
                            <b-form-select
                                id="combined-monthly-income"
                                v-model="form.combined_monthly_income"
                                :state="
                                    errors.combined_monthly_income
                                        ? false
                                        : null
                                "
                                :options="monthly_incomes"
                                :disabled="true"
                            ></b-form-select>
                            <b-form-invalid-feedback
                                :state="
                                    errors.combined_monthly_income
                                        ? false
                                        : null
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
                            label-class="required"
                        >
                            <b-form-select
                                id="internet-connection"
                                v-model="form.internet_connection"
                                :state="
                                    errors.internet_connection ? false : null
                                "
                                :options="internet_connections"
                                :disabled="true"
                            ></b-form-select>
                            <b-form-invalid-feedback
                                :state="
                                    errors.internet_connection ? false : null
                                "
                            >
                                {{ errors.internet_connection }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="3">
                        <b-form-group
                            label="Other gadget(s) you own aside from cellphone:"
                            label-class="required"
                        >
                            <b-form-checkbox-group
                                id="other-gadgets"
                                v-model="form.owned_gadgets"
                                :state="errors.owned_gadgets ? false : null"
                                :options="gadgets"
                                :disabled="true"
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
                            v-if="form.owned_gadgets.includes('Others')"
                        >
                            <b-form-input
                                type="text"
                                id="others-specified"
                                v-model="form.other_gadgets"
                                :aria-describedby="ariaDescribedby"
                                readonly
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="6">
                        <b-form-group
                            label="Property Location"
                            label-for="property-location"
                            label-class="required"
                        >
                            <b-form-select
                                id="property-location"
                                v-model="form.property_assigned"
                                :state="errors.property_assigned ? false : null"
                                :options="property_locations"
                                :disabled="true"
                            ></b-form-select>
                            <b-form-invalid-feedback
                                :state="errors.property_assigned ? false : null"
                            >
                                {{ errors.property_assigned }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                    <b-col sm="6">
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
                                :disabled="true"
                            ></b-form-file>
                            <div class="mt-3">
                                Selected file:
                                <b>{{
                                    form.contract_file
                                        ? form.contract_file.name
                                        : ""
                                }}</b>
                            </div>
                        </b-form-group>
                    </b-col>
                </b-row>
            </b-form>
        </div>
    </b-container>
</template>

<script>
import { Link } from "@inertiajs/vue2";
export default {
    components: {
        Link,
    },
    props: {
        errors: Object,
        member: Object,
        assigned_employee: String,
    },
    data() {
        return {
            form: this.$inertia.form({
                first_name: this.member.first_name,
                middle_name: this.member.middle_name,
                last_name: this.member.last_name,
                title: this.member.title,
                alias: this.member.alias,
                suffix: this.member.suffix,
                birth_date: this.member.birth_date,
                address: this.member.address,
                secondary_address: this.member.secondary_address,
                nationality: this.member.nationality,
                gender: this.member.gender,
                civil_status: this.member.civil_status,
                company_name: this.member.company_name,
                company_number: this.member.company_number,
                occupation: this.member.occupation,
                email: this.member.email,
                mobile_number_one: this.member.mobile_number_one,
                mobile_number_two: this.member.mobile_number_two,
                telephone: this.member.telephone,
                fax: this.member.fax,
                combined_monthly_income: this.member.combined_monthly_income,
                internet_connection: this.member.internet_connection,
                owned_gadgets: this.member.owned_gadgets,
                other_gadgets: this.member.other_gadgets,
                spouse_occupation: this.member.spouse_occupation,
                nature_of_business: this.member.nature_of_business,
                property_assigned: this.member.property_assigned,
                contract_file: this.member.contract_file,
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
};
</script>
