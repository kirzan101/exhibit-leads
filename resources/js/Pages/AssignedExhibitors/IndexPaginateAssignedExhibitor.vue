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
                        <div v-if="selectedIds.length > 0">
                            <div
                                v-if="
                                    check_access('assign-exhibitors', 'create')
                                "
                            >
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
                            <div
                                v-if="
                                    check_access('assign-exhibitors', 'create')
                                "
                            >
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

            <AssignedExhibitorPaginateTable
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
import RemoveModal from "../../Components/Modals/RemoveModal.vue";
import AssignedExhibitorPaginateTable from "../../Components/PaginateTable/AssignedExhibitorPaginateTable.vue";

export default {
    components: {
        Link,
        AssignEmployeeModal,
        RemoveModal,
        AssignedExhibitorPaginateTable,
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
    },
    data() {
        return {
            fields: [
                { key: "selected", label: "selected", sortable: false },
                { key: "row_count", label: "row_count", sortable: false },
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
                    key: "assigned_exhibitor.created_at",
                    label: "Assigned Date",
                    sortable: false,
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
            return this.$page.props.auth.user.employee.user_group.code;
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

            router.post("/reassign-exhibitor", this.form);
            this.selectedIds = [];
        },
        submitRemove() {
            this.form.lead_ids = this.selectedIds;

            this.$bvModal.hide("remove-modal");

            router.post("/remove-assign-exhibitor", this.form);
            this.selected_employee = "";
            this.selectedIds = [];
        },
    },
};
</script>
