<template>
    <div>
        <Head>
            <title>Leads Status</title>
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
                        Leads Status
                    </div>
                    <div class="col-sm-6">
                        &nbsp;
                    </div>
                </div>
            </h5>

            <LeadPaginateTable
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
                :exhibitors="exhibitors"
                :exhibitor="exhibitor"
                :occupationName="occupation"
                :venueId="venue_id"
                :sourceName="source_name"
                @toggle-load-data="loadData"
                @selected-ids="getSelectedIds"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import LeadPaginateTable from "../../Components/PaginateTable/LeadPaginateTable.vue";
import AssignEmployeeModal from "../../Components/Modals/AssignEmployeeModal.vue";
import AssignExhibitorModal from "../../Components/Modals/AssignExhibitorModal.vue";

export default {
    components: {
        Link,
        LeadPaginateTable,
        AssignEmployeeModal,
        AssignExhibitorModal,
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
        exhibitors: Array,
        exhibitor: Number,
        occupation: String,
        venue_id: String,
        source_name: String,
    },
    data() {
        return {
            fields: [
                { key: "row_count", label: "row_count", sortable: false },
                { key: "lead_full_name", label: "Name", isSortable: true },
                { key: "occupation", label: "Occupation", isSortable: true },
                { key: "mobile_number", label: "Mobile No." },
                { key: "source_complete", label: "Source" },
                { key: "venue.name", label: "Venue" },
                { key: "assigned_employee.lead_status", label: "Booker Status" },
                { key: "assigned_confirmer.lead_status", label: "Condfirmer Status" },
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
                ],
            });
        },
        getSelectedIds(data) {
            this.selectedIds = data;
        },
        submitAssigned(selectedEmployeeId) {
            // assign leads to an employee
            this.form.employee_id = selectedEmployeeId; // get id from modal component
            this.form.lead_ids = this.selectedIds;

            this.$bvModal.hide("assign-modal");

            router.post("/assigned-employees", this.form);
            this.selectedIds = [];
        },
        submitAssignedExhibitor(selectedExhibitorId) {
            // assign leads to an exhibitor
            this.form.employee_id = selectedExhibitorId;
            this.form.lead_ids = this.selectedIds;

            this.$bvModal.hide("assign-exhibitor-modal");

            router.post("/assign-exhibitor", this.form);
            this.selectedIds = [];
        },
    },
};
</script>
