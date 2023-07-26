<template>
    <div>
        <Head>
            <title>Confirmed</title>
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

        <b-container fluid>
            <h5>
                <div class="row">
                    <div class="col-sm-6">
                        Confirmed | <Link class="btn btn-secondary" type="button" href="/assigned-confirmers">Back</Link>
                    </div>
                    <div class="col-sm-6">
                        <div v-if="check_access('invites', 'create')">
                            <b-button
                                class="btn btn-danger m-1"
                                v-b-modal.remove-modal
                                style="float: right"
                                align-v="end"
                                v-if="selected_lead.length > 0"
                                >Remove</b-button
                            >
                            <b-button
                                class="btn btn-danger m-1"
                                v-b-modal.remove-modal
                                style="float: right"
                                align-v="end"
                                disabled
                                v-else
                                >Remove</b-button
                            >
                        </div>
                        <!-- remove invitee -->
                        <b-modal title="Notice:" id="remove-modal">
                            <p>Remove to confirmed?</p>
                            <template #modal-footer>
                                <b-button
                                    variant="danger"
                                    type="button"
                                    @click="$bvModal.hide('remove-modal')"
                                    >Close</b-button
                                >
                                <b-button
                                    variant="success"
                                    type="button"
                                    @click="submitRemove()"
                                    v-if="selected_employee != ''"
                                    >Yes</b-button
                                >
                                <b-button
                                    variant="success"
                                    type="button"
                                    disabled
                                    v-else
                                    >Yes</b-button
                                >
                            </template>
                        </b-modal>

                        <!-- assign to confirmer -->
                        <b-modal title="Assign to Confirmer" id="assign-modal">
                            <b-form @submit.prevent="submitAssigned">
                                <b-form-select
                                    v-model="selected_employee"
                                    :options="employeeList"
                                    size="sm"
                                    class="mt-3"
                                ></b-form-select>
                            </b-form>
                            <template #modal-footer>
                                <b-button
                                    variant="danger"
                                    type="button"
                                    @click="$bvModal.hide('assign-modal')"
                                    >Close</b-button
                                >
                                <b-button
                                    variant="success"
                                    type="button"
                                    @click="submitAssigned()"
                                    v-if="selected_employee != ''"
                                    >Submit</b-button
                                >
                                <b-button
                                    variant="success"
                                    type="button"
                                    disabled
                                    v-else
                                    >Submit</b-button
                                >
                            </template>
                        </b-modal>
                    </div>
                </div>
            </h5>

            <br />

            <LeadTable
                :fields="fields"
                :items="leads"
                :per_page="per_page"
                :occupation_list="occupation_list"
                @selected_lead="getSelectedLead($event)"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import LeadTable from "../../Components/LeadTable.vue";

export default {
    components: {
        Link,
        LeadTable,
    },
    props: {
        leads: Array,
        employees: Array,
        per_page: Number,
        occupation_list: Array,
    },
    data() {
        return {
            showDismissibleAlert: false,
            fields: [
                { key: "selected", label: "selected", sortable: false },
                // {
                //     key: "id",
                //     label: "Id",
                //     sortable: true,
                //     sortDirection: "desc",
                // },
                { key: "first_name", label: "First name", sortable: true },
                { key: "last_name", label: "Last name", sortable: true },
                { key: "occupation", label: "Occupation", sortable: true },
                { key: "address", label: "Address", sortable: true },
                // {
                //     key: "is_assigned",
                //     label: "Is Assigned",
                //     sortable: false,
                //     formatter: (value, key, item) => {
                //         return value ? "Yes" : "No";
                //     },
                // },
                // { key: "actions", label: "Actions" },
                {
                    key: "assigned_confirmer_name",
                    label: "Assigned Confirmer",
                    sortable: true,
                },
            ],
            selected_lead: [],
            form: {
                lead_ids: [],
            },
            assign_form: {
                lead_ids: [],
                employee_id: ''
            },
            alert: false,
            selected_employee: '',
        };
    },
    computed: {
        employeeList() {
            return this.employees.map((employee) => {
                return {
                    value: employee.id,
                    text: employee.last_name + ", " + employee.first_name,
                };
            });
        },
    },
    methods: {
        updatedPerPage(value) {
            this.per_page = value;
        },
        getSelectedLead(data) {
            this.selected_lead = data;
        },
        submitRemove() {
            this.form.lead_ids = this.selected_lead;

            this.$bvModal.hide("remove-modal");

            router.post("/confirmed/remove", this.form);
            this.selected_lead = [];
        },
        submitAssigned() {
            this.assign_form.lead_ids = this.selected_lead;
            this.assign_form.employee_id = this.selected_employee;

            this.$bvModal.hide("assign-modal");

            router.post("/assign-confirmer", this.assign_form);
            this.selected_lead = [];
            this.selected_employee = ''
        },
        check_access(module, type) {
            let permissions = this.$page.props.auth.permissions;

            let access = permissions
                .filter((item) => item.module === module)
                .map((element) => ({
                    module: element.module,
                    type: element.type,
                }));

            return access.some((item) => item.type === type);
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
