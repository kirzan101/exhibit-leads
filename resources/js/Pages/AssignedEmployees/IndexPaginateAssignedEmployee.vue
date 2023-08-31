<template>
    <div>
        <Head>
            <title>Assigned Leads</title>
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
                    <div class="col-sm-6">Assigned Leads</div>
                    <div class="col-sm-6">
                        <div v-if="selectedIds.length > 0">
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
                    </div>
                </div>
            </h5>

            <RemoveModal
                message="Remove Assigned?"
                @submit-remove="submitRemove"
            />
            <AssignEmployeeModal
                title="Re-assign leads"
                :employees="employees"
                @submit-assigned-employee="submitAssigned"
            />

            <AssignedEmployeePaginateTable
                :items="items"
                :fields="fields"
                :sort_by="sortBy"
                :sort_desc="sortDesc"
                :search_filter="search"
                :isBusy="is_busy"
                :module="module"
                :employees="employees"
                :occupation_list="occupation_list"
                :venues="venues"
                :sources="sources"
                :status_list="status_list"
                :occupationName="occupation"
                :venueId="venue_id"
                :sourceName="source_name"
                :startTo="start_to"
                :endTo="end_to"
                :leadStatus="lead_status"
                @toggle-load-data="loadData"
                @selected-ids="getSelectedIds"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import AssignEmployeeModal from "../../Components/Modals/AssignEmployeeModal.vue";
import AssignedEmployeePaginateTable from "../../Components/PaginateTable/AssignedEmployeePaginateTable.vue";
import RemoveModal from "../../Components/Modals/RemoveModal.vue";

export default {
    components: {
        Link,
        AssignedEmployeePaginateTable,
        AssignEmployeeModal,
        RemoveModal,
    },
    props: {
        sortBy: String,
        sortDesc: Boolean,
        search: String,
        module: String,
        items: Object,
        employees: Array,
        occupation_list: Array,
        venues: Array,
        sources: Array,
        status_list: Array,
        occupation: String,
        venue_id: String,
        source_name: String,
        start_to: String,
        end_to: String,
        lead_status: String,
    },
    data() {
        return {
            fields: [
                { key: "selected", label: "selected" },
                {
                    key: "lead_full_name",
                    label: "Lead name",
                    isSortable: true,
                },
                { key: "occupation", label: "Occupation", isSortable: true },
                {
                    key: "mobile_number",
                    label: "Mobile Number",
                    isSortable: false,
                },
                { key: "venue.name", label: "Venue", isSortable: true },
                { key: "source_complete", label: "Source", isSortable: true },
                {
                    key: "assigned_employee.lead_status",
                    label: "Status",
                    isSortable: true,
                },
                {
                    key: "assigned_employee_name",
                    label: "Assigned To",
                },
                {
                    key: "assigned_employee.created_at",
                    label: "Assigned Date",
                },
                { key: "actions", label: "Actions" },
            ],
            alert: false,
            is_busy: false,
            selectedIds: [],
            form: {
                employee_id: "",
                lead_ids: [],
            },
        };
    },
    computed: {
        user_group() {
            return this.$page.props.auth.user.employee.user_group.name;
        },
    },
    methods: {
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
        loadData(filter) {
            router.reload({
                data: filter,
                only: [
                    "items",
                    "sortBy",
                    "sortDesc",
                    "search",
                    "occupation",
                    "venue_id",
                    "source_name",
                    "start_to",
                    "end_to",
                    "lead_status",
                ],
            });
        },
        getSelectedIds(data) {
            this.selectedIds = data;
        },
        submitAssigned(data) {
            // assign leads to an employee
            this.form.employee_id = data; // get id from modal component
            this.form.lead_ids = this.selectedIds;

            this.$bvModal.hide("assign-modal");

            router.post("/reassign-employee", this.form);
            this.selectedIds = [];
        },
        submitRemove() {
            this.form.lead_ids = this.selectedIds;

            this.$bvModal.hide("remove-modal");

            router.post("/remove-assign", this.form);
            this.selected_employee = "";
            this.selected_lead = [];
        },
    },
};
</script>
