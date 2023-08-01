<template>
    <div>
        <Head>
            <title>Assigned for Confirmation</title>
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
                    <div class="col-sm-6">Assigned for Confirmation | <Link class="btn btn-success" type="button" href="/confirmed">View confirmed</Link></div>
                    <div class="col-sm-6">
                        <div v-if="selected_lead.length > 0">
                            <div v-if="check_access('assigns', 'create')">
                                <b-button
                                    class="btn btn-danger mx-1 my-1"
                                    v-b-modal.remove-modal
                                    style="float: right"
                                    align-v="end"
                                    >Remove</b-button
                                >
                                <b-button
                                    class="btn btn-info mx-1 my-1"
                                    v-b-modal.assign-modal
                                    style="float: right"
                                    align-v="end"
                                    >Re-assign</b-button
                                >
                            </div>
                        </div>
                        <div v-else>
                            <div v-if="check_access('assigns', 'create')">
                                <b-button
                                    class="btn btn-danger mx-1 my-1"
                                    style="float: right"
                                    align-v="end"
                                    disabled
                                    >Remove</b-button
                                >
                                <b-button
                                    class="btn btn-info mx-1 my-1"
                                    style="float: right"
                                    align-v="end"
                                    disabled
                                    >Re-assign</b-button
                                >
                            </div>
                        </div>
                        <!-- Reassign modal start -->
                        <b-modal title="Reassign to Confirmer" id="assign-modal">
                            <b-form @submit.prevent="submitAssigned">
                                <b-form-select
                                    v-model="selected_confirmer"
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
                                    v-if="selected_confirmer != ''"
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
                        <!-- Reassign modal end -->

                        <!-- Remove modal start -->
                        <b-modal title="Notice:" id="remove-modal">
                            <h5>Remove Assigned?</h5>
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
                                    >Submit</b-button
                                >
                            </template>
                        </b-modal>
                        <!-- Remove modal End -->
                    </div>
                </div>
            </h5>

            <br />

            <AssignedConfirmerTable
                :fields="fields"
                :items="leads"
                :per_page="per_page"
                :properties="properties"
                :status_list="status_list"
                :venue_list="venue_list"
                @selected_lead="getSelectedLead($event)"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import AssignedEmployeeTable from "../../Components/AssignedEmployeeTable.vue";
import AssignedConfirmerTable from "../../Components/AssignedConfirmerTable.vue";

export default {
    components: {
        Link,
        AssignedEmployeeTable,
        AssignedConfirmerTable,
    },
    props: {
        leads: Array,
        employees: Array,
        properties: Array,
        per_page: Number,
        status_list: Array,
        venue_list: Array
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
                {
                    key: "lead_full_name",
                    label: "Lead name",
                    sortable: true,
                },
                { key: "venue.name", label: "Venue", sortable: true },
                {
                key: "assigned_confirmer_name",
                    label: "Assigned To",
                    sortable: true,
                },
                { key: "presentation_date", label: "Presentation Date", sortable: true },
                {
                key: "mobile_number",
                    label: "Mobile number",
                    sortable: false,
                },
                { key: "actions", label: "Actions" },
            ],
            selected_confirmer: "",
            selected_lead: [],
            selected_lead_confirmer_id: [],
            form: {
                employee_id: "",
                lead_ids: [],
            },
            remarks: "",
            leads_selected: [],
            checked_all: false,
            alert: false,
        };
    },
    watch: {
        showDismissibleAlert() {
            this.$page.props.flash.error = "";
            this.$page.props.flash.success = "";
            this.$page.props.flash.message = "";
        },
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
        getSelectedEmployee(data) {
            this.selected_lead_confirmer_id = data;
        },
        submitAssigned() {
            this.form.employee_id = this.selected_confirmer;
            this.form.lead_ids = this.selected_lead;

            this.$bvModal.hide("assign-modal");

            router.post("/reassign-confirmer", this.form);
            this.selected_confirmer = "";
            this.selected_lead = [];
        },
        submitRemove() {
            this.form.lead_ids = this.selected_lead;

            this.$bvModal.hide("remove-modal");

            router.post("/remove-assign-confirmer", this.form);
            this.selected_confirmer = "";
            this.selected_lead = [];
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
