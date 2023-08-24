<template>
    <div>
        <Head>
            <title>Assigned Leads for Exhibitor</title>
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
                    <div class="col-sm-6">Assigned Leads for Exhibitor</div>
                    <div class="col-sm-6">
                        <div v-if="selected_lead.length > 0">
                            <div v-if="check_access('assign-exhibitors', 'create')">
                                <b-button
                                    class="btn btn-danger mx-1 my-1"
                                    v-b-modal.remove-modal
                                    style="float: right"
                                    align-v="end"
                                    >Remove</b-button
                                >
                            </div>
                        </div>
                        <div v-else>
                            <div v-if="check_access('assign-exhibitors', 'create')">
                                <b-button
                                    class="btn btn-danger mx-1 my-1"
                                    style="float: right"
                                    align-v="end"
                                    disabled
                                    >Remove</b-button
                                >
                            </div>
                        </div>
                        <!-- Reassign modal start -->
                        <b-modal title="Reassign to Employee" id="assign-modal">
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

            <AssignedExhibitorTable
                :fields="fields"
                :items="leads"
                :per_page="per_page"
                :status_list="status_list"
                :occupation_list="occupation_list"
                :venue_list="venue_list"
                :employees="employees"
                @selected_lead="getSelectedLead($event)"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import AssignedExhibitorTable from "../../Components/AssignedExhibitorTable.vue";

export default {
    components: {
        Link,
        AssignedExhibitorTable
    },
    props: {
        leads: Array,
        employees: Array,
        per_page: Number,
        status_list: Array,
        occupation_list: Array,
        venue_list: Array,
    },
    data() {
        return {
            showDismissibleAlert: false,
            fields: [
                { key: "selected", label: "selected", sortable: false },
                {
                    key: "lead_full_name",
                    label: "Lead name",
                    sortable: true,
                },
                { key: "occupation", label: "Occupation", sortable: true },
                { key: "venue.name", label: "Venue", sortable: true },
                {
                    key: "assigned_exhibitor_name",
                    label: "Assigned To",
                    sortable: true,
                },
                {
                    key: "assigned_exhibitor.created_at", label: "Assigned Date", sortable: false
                },
                { key: "actions", label: "Actions" },
            ],
            selected_employee: "",
            selected_lead: [],
            selected_lead_employee_id: [],
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
            this.selected_lead_employee_id = data;
        },
        submitAssigned() {
            this.form.employee_id = this.selected_employee;
            this.form.lead_ids = this.selected_lead;

            this.$bvModal.hide("assign-modal");

            router.post("/reassign-exhibitor", this.form);
            this.selected_employee = "";
            this.selected_lead = [];
        },
        submitRemove() {
            this.form.lead_ids = this.selected_lead;

            this.$bvModal.hide("remove-modal");

            router.post("/remove-assign-exhibitor", this.form);
            this.selected_employee = "";
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
        formatedDate(date) {
            let formatted_date = new Date(date);

            return formatted_date.getDate();
        }
    },
};
</script>
