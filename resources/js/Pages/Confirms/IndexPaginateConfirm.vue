<template>
    <div>
        <Head>
            <title>Confirms</title>
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
                    <div class="col-sm-6">Confirms</div>
                    <div class="col-sm-6">
                        <div v-if="check_access(module, 'create')">
                            <b-button
                                class="btn btn-danger mx-1 my-1"
                                v-b-modal.remove-modal
                                style="float: right"
                                align-v="end"
                                v-if="selectedIds > 0"
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
                    </div>
                </div>
            </h5>

            <RemoveModal
                message="Remove Assigned?"
                @submit-remove="submitRemove"
            />

            <ConfirmPaginateTable
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
                :confirmer_status_list="confirmer_status_list"
                :occupationName="occupation"
                :venueId="venue_id"
                :sourceName="source_name"
                :startTo="start_to"
                :endTo="end_to"
                :startTimeTo="start_time_to"
                :endTimeTo="end_time_to"
                :leadStatus="lead_status"
                :employeeId="employee_id"
                @toggle-load-data="loadData"
                @selected-ids="getSelectedIds"
                @toggle-clear-notif="clearNotif"
            />

            <ConfirmerVenueModal
                v-if="employee_venues.length == 0 && user_group == 'CONFIRMERS'"
                :employee_id="employee_id"
                :venue_list="venues"
                :employee_venue_list="employee_venues"
                title="Notice:"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import AssignedEmployeePaginateTable from "../../Components/PaginateTable/AssignedEmployeePaginateTable.vue";
import ConfirmPaginateTable from "../../Components/PaginateTable/ConfirmPaginateTable.vue";
import RemoveModal from "../../Components/Modals/RemoveModal.vue";
import ConfirmerVenueModal from "../../Components/Modals/ConfirmerVenueModal.vue";

export default {
    components: {
        Link,
        ConfirmPaginateTable,
        RemoveModal,
        ConfirmerVenueModal,
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
        confirmer_status_list: Array,
        occupation: String,
        venue_id: String,
        source_name: String,
        start_to: String,
        end_to: String,
        start_time_to: String,
        end_time_to: String,
        lead_status: String,
        employee_id: String,
        employee_venues: Array,
    },
    data() {
        return {
            fields: [
                { key: "selected", label: "selected" },
                { key: "row_count", label: "row_count", sortable: false },
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
                    key: "refer_by",
                    label: "Refer By",
                },
                {
                    key: "assigned_employee_name",
                    label: "Booker",
                },
                {
                    key: "presentation_datetime",
                    label: "Presentation date",
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
            console.log("here");
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
                    "start_time_to",
                    "end_time_to",
                    "lead_status",
                    "employee_id",
                ],
            });
        },
        getSelectedIds(data) {
            this.selectedIds = data;
        },
        submitRemove() {
            this.form.lead_ids = this.selectedIds;

            this.$bvModal.hide("remove-modal");

            router.post("/confirmer/done/cancel", this.form);
            this.selected_lead = [];
        },
    },
};
</script>
