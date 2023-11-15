<template>
    <b-container fluid>
        <!-- User Interface controls -->
        <b-row>
            <!-- first filter -->
            <b-col sm="6" md="6" class="my-1">
                <!-- <b-form-group
                    label="Status"
                    label-for="status-select"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-form-select
                        id="status-select"
                        v-model="filter.lead_status"
                        :options="lead_status_options"
                        @change="filterTable"
                        size="sm"
                    ></b-form-select>
                </b-form-group> -->
            </b-col>

            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="Filter"
                    label-for="filter-input"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-input-group size="sm">
                        <b-form-input
                            id="filter-input"
                            v-model="filter.search"
                            type="text"
                            placeholder="Type to Search"
                            v-debounce:500ms="filterTable"
                            maxlength="15"
                        ></b-form-input>

                        <b-input-group-append>
                            <b-button
                                :disabled="!filter.search"
                                @click="
                                    filter.search = '';
                                    filterTable();
                                "
                                >Clear</b-button
                            >
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>
            <!-- first filter end-->

            <!-- second filter start -->
            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="Venue"
                    label-for="venue-select"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-form-select
                        id="venue-select"
                        v-model="filter.venue_id"
                        :options="venue_options"
                        @change="filterTable"
                        size="sm"
                    ></b-form-select>
                </b-form-group>
            </b-col>

            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="Source"
                    label-for="source-select"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-form-select
                        id="source-select"
                        v-model="filter.source_name"
                        :options="source_options"
                        @change="filterTable"
                        size="sm"
                    ></b-form-select>
                </b-form-group>
            </b-col>
            <!-- second filter end -->

            <!-- third filter start -->
            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="Sort"
                    label-for="sort-by-select"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                    v-slot="{ ariaDescribedby }"
                >
                    <b-input-group size="sm">
                        <b-form-select
                            id="sort-by-select"
                            v-model="filter.sort_by"
                            :options="sortOptions"
                            :aria-describedby="ariaDescribedby"
                            class="w-75"
                            @change="filterTable"
                        >
                        </b-form-select>

                        <b-form-select
                            v-model="filter.is_sort_desc"
                            :disabled="!filter.sort_by"
                            :aria-describedby="ariaDescribedby"
                            size="sm"
                            class="w-25"
                            @change="filterTable"
                        >
                            <option :value="false">Asc</option>
                            <option :value="true">Desc</option>
                        </b-form-select>
                    </b-input-group>
                </b-form-group>
            </b-col>

            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="Occupation"
                    label-for="occupation-select"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-form-select
                        id="occupation-select"
                        v-model="filter.occupation"
                        :options="occupation_options"
                        @change="filterTable"
                        size="sm"
                    ></b-form-select>
                </b-form-group>
            </b-col>
            <!-- third filter end -->

            <!-- fourth filter start -->
            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="Start to"
                    label-for="start-to"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-form-input
                        type="date"
                        id="start-to"
                        v-model="filter.start_to"
                        v-debounce:500ms="filterDate"
                    ></b-form-input>
                </b-form-group>
            </b-col>

            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="End to"
                    label-for="end-to"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-form-input
                        type="date"
                        id="end-to"
                        v-model="filter.end_to"
                        v-debounce:500ms="filterDate"
                    ></b-form-input>
                </b-form-group>
            </b-col>
            <!-- fourth filter end -->

            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="Per page"
                    label-for="per-page-select"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-form-select
                        id="per-page-select"
                        v-model="filter.per_page"
                        :options="pageOptions"
                        size="sm"
                        @change="filterTable"
                    ></b-form-select>
                </b-form-group>
            </b-col>

            <b-col sm="6" md="6" class="my-1">
                <b-pagination
                    v-model="filter.page"
                    :total-rows="rows"
                    :per-page="perPage"
                    align="fill"
                    size="sm"
                    class="my-0"
                ></b-pagination>
            </b-col>
        </b-row>

        <br />
        <i
            >Showing <b>{{ filteredRows }}</b> items of
            <b>{{ rows }}</b> records</i
        >
        <!-- Main table element -->
        <b-table
            :items="items.data"
            :fields="fields"
            :current-page="currentPage"
            :busy="is_Busy"
            stacked="md"
            show-empty
            small
            :no-local-sorting="true"
            @row-clicked="select"
        >
            <!-- loader start -->
            <template #table-busy>
                <div class="text-center text-info my-2">
                    <b-spinner class="align-middle mr-2"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
            <!-- loader end -->

            <!-- checkbox start -->
            <template #head(selected)="column">
                <b-form-checkbox
                    v-model="checkedAll"
                    @change="selectAll"
                ></b-form-checkbox>
            </template>

            <template v-slot:cell(selected)="row">
                <b-form-group>
                    <b-form-checkbox
                        v-model="selectedIds"
                        :value="row.item.id"
                        :id="row.item.id + '-id'"
                    ></b-form-checkbox>
                </b-form-group>
            </template>
            <!-- checkbox end -->

            <!-- actions start -->
            <template #cell(actions)="row">
                <Link
                    :href="'assigned-exhibitors/' + row.item.id"
                    class="btn m-1 btn-info"
                    type="button"
                    v-if="check_access('assign-exhibitors', 'read')"
                    >Show</Link
                >
            </template>
            <!-- actions end -->

            <!-- format assigned date start -->
            <template #cell(assigned_exhibitor.created_at)="row">
                {{ formatDate(row.item.assigned_exhibitor.created_at) }}
            </template>
            <!-- format assigned date end -->

            <!-- row count start -->
            <template #head(row_count)="column"> # </template>

            <template v-slot:cell(row_count)="row">
                {{ rowNumbering(row.index) }}
            </template>
            <!-- row count end -->
        </b-table>
    </b-container>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import RemarksModal from "../Modals/RemarksModal.vue";
import DoneModal from "../Modals/DoneModal.vue";

export default {
    props: {
        fields: Array,
        module: String,
        sort_by: String,
        sort_desc: Boolean,
        search_filter: String,
        items: Object,
        isBusy: Boolean,
        module: String,
        occupation_list: Array,
        venues: Array,
        sources: Array,
        status_list: Array,
        occupationName: String,
        venueId: String,
        sourceName: String,
        startTo: String,
        endTo: String,
        leadStatus: String,
    },
    components: {
        Link,
        RemarksModal,
        DoneModal,
    },
    data() {
        return {
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot (100)" }],
            is_Busy: this.isBusy,
            filter: {
                page: this.items.meta.current_page,
                sort_by: this.sort_by,
                is_sort_desc: this.sort_desc,
                per_page: this.items.meta.per_page,
                search: this.search_filter,
                occupation: this.occupationName,
                venue_id: this.venueId,
                source_name: this.sourceName,
                start_to: this.startTo,
                end_to: this.endTo,
                // lead_status: this.leadStatus,
            },
            selectedIds: [],
            checkedAll: false,
            occupation_options: [
                { value: null, text: "-- select --" },
                ...this.occupation_list.map((item) => {
                    return { value: item.occupation, text: item.occupation };
                }),
            ],
            venue_options: [
                { value: null, text: "-- select --" },
                ...this.venues.map((item) => {
                    return { value: item.id, text: item.name };
                }),
            ],
            source_options: [
                { value: null, text: "-- select --" },
                ...this.sources.map((item) => {
                    return { value: item.source, text: item.source };
                }),
            ],
            lead_status_options: [
                { value: null, text: "-- select --" },
                ...this.status_list.map((item) => {
                    return {
                        value: item.name,
                        text: item.name + " " + "(" + item.code + ")",
                    };
                }),
            ],
            form: {
                remarks: "",
                lead_id: "",
                // lead_status: "",
                venue_id: "",
                presentation_date: null,
                presentation_time: null,
            },
            updated_by: "",
            status: true,
            employee_type: "employee",
        };
    },
    computed: {
        rows() {
            return this.items.meta.total;
        },
        filteredRows() {
            return this.items.data.length;
        },
        currentPage() {
            return this.items.meta.current_page;
        },
        perPage() {
            return this.items.meta.per_page;
        },
        sortOptions() {
            return [
                { text: "-- select --", value: null },
                // ...this.fields
                //     .filter((f) => f.isSortable)
                //     .map((f) => {
                //         return { text: f.label, value: f.key };
                //     }),
            ];
        },
    },
    watch: {
        "filter.page": function () {
            this.filterTable();
        },
        selectedIds() {
            return this.$emit("selected-ids", this.selectedIds);
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
        filterTable() {
            this.is_Busy = true;

            this.$emit("toggle-load-data", this.filter);
        },
        filterDate() {
            this.is_Busy = true;

            if (
                this.filter.start_to != null ||
                (this.filter.start_to != "" && this.filter.end_to != null) ||
                this.filter.end_to != ""
            ) {
                this.$emit("toggle-load-data", this.filter);
            }
        },
        selectAll() {
            this.selectedIds = [];
            if (this.checkedAll) {
                for (let i in this.items.data) {
                    this.selectedIds.push(this.items.data[i].id);
                }
            }
        },
        select(item) {
            if (this.selectedIds.includes(item.id)) {
                const index = this.selectedIds.indexOf(item.id);
                if (index > -1) {
                    this.selectedIds.splice(index, 1);
                }
            } else {
                this.selectedIds.push(item.id);
            }
        },
        modifyRemarks(form) {
            router.post("/remarks", form);
            this.$bvModal.hide("remarks-modal");
        },
        selectedLead(data) {
            this.form.lead_id = data.id;
            this.form.remarks = data.assigned_exhibitor.remarks;
            // this.form.lead_status = data.assigned_exhibitor.lead_status;
            this.form.venue_id = data.venue_id;
            this.updated_by =
                data.updated_by.length != 0
                    ? data.updated_by.last_name +
                      ", " +
                      data.updated_by.first_name
                    : "";
            this.form.presentation_date = data.presentation_date;
            this.form.presentation_time = data.presentation_time;
        },
        formatDate(value) {
            let date_value = new Date(value);

            return (
                date_value.toLocaleDateString("en-US") +
                " " +
                date_value.toLocaleTimeString("en-US")
            );
        },
        submitDone(form) {
            router.post("/done", form);

            this.emptyForm();

            this.$bvModal.hide("done-modal");
        },
        emptyForm() {
            //empty form
            this.form.lead_id = null;
            this.form.remarks = null;
            // this.form.lead_status = null;
            this.form.venue_id = null;
            this.updated_by = null;
            this.form.presentation_date = null;
            this.form.presentation_time = null;
        },
        rowNumbering(rowCount) {
            if (this.sort_desc) {
                // get the total then minus to the current page number
                // sample computation 50(total) - (1(from) + 0(index)) + 1 = 50
                // the objective is to start to the last count of rows,
                // add 1 to make the counting start to the base count
                return (
                    Number(this.items.meta.total) -
                    (Number(this.items.meta.from) + Number(rowCount)) +
                    1
                );
            }

            //counts the number of the rows
            //sample computation 1(from) + 0(index) = 1
            return Number(this.items.meta.from) + Number(rowCount);
        },
    },
};
</script>
