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
                        Confirmed |
                        <b-button
                            class="btn btn-success m-1"
                            v-b-modal.download-modal
                            align-v="end"
                            v-if="items.data.length > 0"
                            >Download</b-button
                        >
                        <b-button
                            class="btn btn-success m-1"
                            align-v="end"
                            disabled
                            v-else
                            >Download</b-button
                        >
                    </div>
                    <div class="col-sm-6">
                        <div v-if="check_access('confirms', 'create')">
                            <b-button
                                class="btn btn-danger m-1"
                                v-b-modal.remove-modal
                                style="float: right"
                                align-v="end"
                                v-if="selectedIds.length > 0"
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

            <br />

            <RemoveModal
                message="Remove Confirmed?"
                @submit-remove="submitRemove"
            />

            <DownloadModal
                message="Confirm Download?"
                @submit-download="submitDownload"
            />

            <ConfirmedPaginateTable
                :sort_by="sortBy"
                :sort_desc="sortDesc"
                :search_filter="search"
                :occupationName="occupation"
                :venueId="venue_id"
                :sourceName="source_name"
                :occupation_list="occupation_list"
                :items="items"
                :fields="fields"
                :module="module"
                :isBusy="is_busy"
                :startTo="start_to"
                :endTo="end_to"
                :employeeId="employee_id"
                :confirmerId="confirmer_id"
                :exhibitorId="exhibitor_id"
                :venues="venues"
                :sources="sources"
                :employees="employees"
                :confirmers="confirmers"
                :exhibitors="exhibitors"
                :status_list="status_list"
                :confirmer_status_list="confirmer_status_list"
                @toggle-load-data="loadData"
                @selected-ids="getSelectedIds"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import ConfirmedPaginateTable from "../../Components/PaginateTable/ConfirmedPaginateTable.vue";
import RemoveModal from "../../Components/Modals/RemoveModal.vue";
import DownloadModal from "../../Components/Modals/DownloadModal.vue";

export default {
    components: {
        Link,
        ConfirmedPaginateTable,
        RemoveModal,
        DownloadModal,
    },
    props: {
        sortBy: String,
        sortDesc: Boolean,
        search: String,
        occupation: String,
        venue_id: String,
        source_name: String,
        start_to: String,
        end_to: String,
        employee_id: String,
        confirmer_id: String,
        exhibitor_id: String,
        items: Object,
        occupation_list: Array,
        venues: Array,
        sources: Array,
        employees: Array,
        exhibitors: Array,
        confirmers: Array,
        status_list: Array,
        confirmer_status_list: Array,
        module: String,
    },
    data() {
        return {
            fields: [
                { key: "selected", label: "selected", isSortable: false },
                { key: "row_count", label: "row_count", sortable: false },
                { key: "lead_full_name", label: "Lead name", isSortable: true },
                { key: "occupation", label: "Occupation", isSortable: true },
                { key: "venue.name", label: "Venue", isSortable: true },
                { key: "source_complete", label: "Source", isSortable: true },
                {
                    key: "assigned_employee_name",
                    label: "Booker",
                    isSortable: false,
                },
                {
                    key: "assigned_exhibitor_name",
                    label: "Exhibitor",
                    isSortable: false,
                },
                {
                    key: "assigned_confirmer_name",
                    label: "Confirmer",
                    isSortable: false,
                },
                {
                    key: "assigned_confirmer.updated_at",
                    label: "Done at",
                    isSortable: true,
                },
                { key: "actions", label: "Actions" },
            ],
            is_busy: false,
            selected_lead: [],
            form: {
                lead_ids: [],
            },
            assign_form: {
                lead_ids: [],
                employee_id: "",
            },
            alert: false,
            selected_employee: "",
            selectedIds: [],
            download_filter: {
                sort_by: this.sort_by,
                is_sort_desc: this.sort_desc,
                search: this.search_filter,
                occupation: this.occupationName,
                venue_id: this.venueId,
                source_name: this.sourceName,
                start_to: this.startTo,
                end_to: this.endTo,
                employee_id: this.employeeId,
                confirmer_id: this.confirmerId,
                exhibitor_id: this.exhibitorId,
            },
        };
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
                    "employee_id",
                    "confirmer_id",
                    "exhibitor_id",
                ],
            });
        },
        getSelectedIds(data) {
            this.selectedIds = data;
        },
        submitRemove() {
            this.form.lead_ids = this.selectedIds;

            this.$bvModal.hide("remove-modal");

            router.post("/confirmed/remove", this.form);
            this.selected_lead = [];
        },
        submitDownload() {
            this.$bvModal.hide("download-modal");
            // window.open("/reports/confirmed", "_blank");

            // console.log(router.get("/reports/confirmed", this.form));
            router.visit("/reports/confirmed", {
                data: this.download_filter,
                method: "get",
                onBefore: (visit) => {
                    // console.log(visit.url.href)
                    window.open(visit.url.href);
                },
            });
        },
    },
};
</script>
