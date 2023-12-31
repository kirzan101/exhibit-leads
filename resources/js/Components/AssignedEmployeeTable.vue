<template>
    <div>
        <b-container fluid>
            <!-- User Interface controls -->
            <b-row>
                <b-col lg="6" class="my-1">
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
                                v-model="filter"
                                type="search"
                                placeholder="Type to Search"
                            ></b-form-input>

                            <b-input-group-append>
                                <b-button
                                    :disabled="!filter"
                                    @click="filter = ''"
                                    >Clear</b-button
                                >
                            </b-input-group-append>
                        </b-input-group>
                    </b-form-group>
                </b-col>

                <b-col lg="6" class="my-1">
                    <b-form-group
                        label="Has Remarks"
                        label-for="has-remarks-select"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                    >
                        <b-form-select
                            id="has-remarks-select"
                            v-model="hasRemarks"
                            :options="['All', 'Yes', 'No']"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>

                <!-- Second filter -->
                <b-col lg="6" class="my-1">
                    <b-form-group
                        label="Start to"
                        label-for="start-to"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                    >
                        <b-form-datepicker
                            id="start-to"
                            v-model="start_to"
                            size="sm"
                            class="mb-2"
                        ></b-form-datepicker>
                    </b-form-group>
                </b-col>

                <b-col lg="6" class="my-1">
                    <b-form-group
                        label="End to"
                        label-for="end-to"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                    >
                        <b-form-datepicker
                            id="end-to"
                            v-model="end_to"
                            size="sm"
                            class="mb-2"
                        ></b-form-datepicker>
                    </b-form-group>
                </b-col>
                <!-- End Second filter -->

                <!-- third filter -->
                <b-col lg="6" class="my-1">
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
                            v-model="occupation"
                            :options="occupation_options"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>

                <b-col lg="6" class="my-1">
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
                            v-model="venue"
                            :options="venue_options"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>
                <!-- End third filter -->

                <b-col sm="5" md="6" class="my-1">
                    <b-form-group
                        label="Per page"
                        label-for="per-page-select"
                        label-cols-sm="6"
                        label-cols-md="4"
                        label-cols-lg="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                    >
                        <b-form-select
                            id="per-page-select"
                            v-model="perPage"
                            :options="pageOptions"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>

                <b-col sm="7" md="6" class="my-1">
                    <b-pagination
                        v-model="currentPage"
                        :total-rows="totalRows"
                        :per-page="perPage"
                        align="fill"
                        size="sm"
                        class="my-0"
                    ></b-pagination>
                </b-col>
            </b-row>

            <!-- Main table element -->
            <b-table
                :items="assignedLeads"
                :fields="fields"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                :sort-direction="sortDirection"
                stacked="md"
                show-empty
                small
                @filtered="onFiltered"
            >
                <template #head(selected)="column">
                    <b-form-checkbox
                        v-model="checkedAll"
                        @change="select"
                        v-if="check_access('assigns', 'create')"
                    ></b-form-checkbox>
                    <span v-else>&nbsp;</span>
                </template>

                <template v-slot:cell(selected)="row">
                    <b-form-group>
                        <b-form-checkbox
                            v-model="selected_ids"
                            :value="row.item.id"
                            :id="row.item.id + '-' + row.item.last_name"
                            :disabled-field="row.item.is_assigned"
                            @change="
                                selectEmployee($event, row.item.employee.id)
                            "
                            v-if="check_access('assigns', 'create')"
                        ></b-form-checkbox>
                    </b-form-group>
                </template>

                <template #cell(name)="row">
                    {{ row.value.first }} {{ row.value.last }}
                </template>

                <template #cell(assigned_employee.created_at)="row">
                    {{ formatDate(row.item.assigned_employee.created_at) }}
                </template>

                <template #cell(actions)="row">
                    <Link
                        :href="'assigned-employees/' + row.item.id"
                        class="btn m-1 btn-info"
                        type="button"
                        v-if="check_access('assigns', 'read')"
                        >Show</Link
                    >
                    <b-button
                        v-b-modal.remarks-modal
                        variant="warning text-white"
                        @click="selectedLead(row.item)"
                        class="m-1"
                        v-if="
                            !row.item.assigned_employee.remarks &&
                            check_access('assigns', 'update')
                        "
                        >Add remarks</b-button
                    >
                    <b-button
                        v-b-modal.remarks-modal
                        variant="danger"
                        @click="selectedLead(row.item)"
                        class="m-1"
                        v-else
                        >Edit remarks</b-button
                    >
                    <b-button
                        v-if="
                            row.item.assigned_employee.remarks &&
                            check_access('assigns', 'update')
                        "
                        v-b-modal.done-modal
                        variant="success"
                        @click="selectedLead(row.item)"
                        class="m-1"
                        >Done</b-button
                    >
                </template>

                <template #row-details="row">
                    <b-card>
                        <ul>
                            <li v-for="(value, key) in row.item" :key="key">
                                {{ key }}: {{ value }}
                            </li>
                        </ul>
                    </b-card>
                </template>
            </b-table>

            <!-- Add Remarks modal -->
            <b-modal id="remarks-modal" title="Remarks">
                <b-form-textarea
                    id="textarea"
                    v-model="remarks"
                    placeholder="Enter something..."
                    rows="3"
                    max-rows="6"
                ></b-form-textarea>
                <p class="mt-2 mb-2">Lead status:</p>
                <b-form-select
                    v-model="lead_status"
                    :options="lead_status_options"
                ></b-form-select>
                <p class="mt-2 mb-2">Venue:</p>
                <b-form-select
                    v-model="venue_id"
                    :options="venue_options"
                ></b-form-select>
                <p class="mt-2 mb-2">Presentation:</p>
                <b-input-group>
                    <b-form-input
                        type="date"
                        id="presentation-date"
                        v-model="presentation_date"
                    ></b-form-input>
                    <b-form-input
                        type="time"
                        id="presentation-time"
                        v-if="presentation_date"
                        v-model="presentation_time"
                    ></b-form-input>
                    <b-form-input
                        type="time"
                        id="presentation-time"
                        disabled
                        v-else
                    ></b-form-input>
                </b-input-group>
                <p class="mt-3" v-if="updated_by !== ''">
                    Last remark by: <b>{{ updated_by }}</b>
                </p>
                <template #modal-footer>
                    <b-button
                        variant="danger"
                        type="button"
                        @click="$bvModal.hide('remarks-modal')"
                        >Close</b-button
                    >
                    <b-button
                        variant="success"
                        type="button"
                        @click="modifyRemarks"
                        v-if="
                            remarks != null &&
                            remarks.length > 0 &&
                            lead_status != null
                        "
                        >Submit</b-button
                    >
                    <b-button variant="success" type="button" disabled v-else
                        >Submit</b-button
                    >
                </template>
            </b-modal>

            <b-modal id="done-modal" title="Notice">
                <p>Mark as Done?</p>
                <template #modal-footer>
                    <b-button
                        variant="danger"
                        type="button"
                        @click="$bvModal.hide('done-modal')"
                        >Close</b-button
                    >
                    <b-button variant="success" type="button" @click="done"
                        >Yes</b-button
                    >
                </template>
            </b-modal>
        </b-container>
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";

export default {
    components: {
        Link,
    },
    props: {
        leads: Array,
        items: Array,
        fields: Array,
        per_page: Number,
        status_list: Array,
        occupation_list: Array,
        venue_list: Array,
    },
    data() {
        return {
            totalRows: 1,
            currentPage: 1,
            perPage: this.per_page,
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
            sortBy: "",
            sortDesc: false,
            sortDirection: "asc",
            filter: null,
            filterOn: ["is_assigned"],
            infoModal: {
                id: "info-modal",
                title: "",
                content: "",
            },
            selected_ids: [],
            selected_employee_ids: [],
            checkedAll: false,
            remarks: "",
            lead_status: null,
            selected_row_id: null,
            form: {
                remarks: "",
                lead_id: "",
                lead_status: "",
                venue_id: "",
                presentation_date: null,
                presentation_time: null
            },
            hasRemarks: "All",
            updated_by: "",
            done_form: {
                status: true,
                lead_id: "",
                employee_type: "employee"
            },
            lead_status_options: [
                { value: null, text: "-- select --" },
                ...this.status_list.map((item) => {
                    return {
                        value: item.name,
                        text: item.name + " " + "(" + item.code + ")",
                    };
                }),
            ],
            start_to: "",
            end_to: "",
            occupation: null,
            occupation_options: [
                { value: null, text: "-- select --" },
                ...this.occupation_list.map((item) => {
                    return {
                        value: item.occupation,
                        text: item.occupation,
                    };
                }),
            ],
            venue: null,
            venue_options: [
                { value: null, text: "-- select --" },
                ...this.venue_list.map((item) => {
                    return {
                        value: item.id,
                        text: item.name,
                    };
                }),
            ],
            venue_id: null, //used in remarks modal
            presentation_date: null,
            presentation_time: null
        };
    },
    watch: {
        selected_ids() {
            return this.$emit("selected_lead", this.selected_ids);
        },
        selected_employee_ids() {
            return this.$emit(
                "selected_lead_employee_id",
                this.selected_employee_ids
            );
        },
        checkedAll() {
            return this.$emit("checkedAll", this.checkedAll);
        },
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields99
            return this.fields
                .filter((f) => f.sortable)
                .map((f) => {
                    return { text: f.label, value: f.key };
                });
        },
        assignedLeads() {
            // remarks filter
            if (this.hasRemarks == "Yes") {
                return this.items.filter((item) => item.assigned_employee.remarks != null);
            } else if (this.hasRemarks == "No") {
                return this.items.filter((item) => item.assigned_employee.remarks == null);
            }

            // assigned date filter
            if (this.start_to != "" && this.end_to != "") {
                return this.items.filter((item) => {
                    const itemDate = new Date(
                        item.assigned_employee.created_at
                    );
                    const start = new Date(this.start_to);
                    const end = new Date(this.end_to);
                    return (
                        itemDate.toLocaleDateString("en-US") >=
                            start.toLocaleDateString("en-US") &&
                        itemDate.toLocaleDateString("en-US") <=
                            end.toLocaleDateString("en-US")
                    );
                });
            }

            // occupation filter
            if (this.occupation) {
                return this.items.filter((item) => {
                    return item.occupation == this.occupation;
                });
            }

            // venue filter
            if (this.venue) {
                return this.items.filter((item) => {
                    return item.venue.id == this.venue;
                });
            }

            return this.items;
        },
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.items.length;
    },
    methods: {
        info(item, index, button) {
            this.infoModal.title = `Row index: ${index}`;
            this.infoModal.content = JSON.stringify(item, null, 2);
            this.$root.$emit("bv::show::modal", this.infoModal.id, button);
        },
        resetInfoModal() {
            this.infoModal.title = "";
            this.infoModal.content = "";
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        select() {
            this.selected_ids = [];
            this.selected_employee_ids = [];
            if (this.checkedAll) {
                console.log("selected all");
                for (let i in this.items
                    .filter((item) => item.is_assigned == true)
                    .slice(0, this.perPage)) {
                    this.selected_ids.push(this.items[i].id);
                    this.selected_employee_ids.push(this.items[i].employee.id);
                }

                // remove duplicate ids
                this.selected_employee_ids = this.selected_employee_ids.filter(
                    (item, index) =>
                        this.selected_employee_ids.indexOf(item) === index
                );
            }
        },
        selectEmployee(event, data) {
            // console.log(event.currentTarget);
            this.selected_employee_ids = data;
        },
        modifyRemarks() {
            this.form.lead_id = this.selected_row_id;
            this.form.remarks = this.remarks;
            this.form.lead_status = this.lead_status;
            this.form.venue_id = this.venue_id;
            this.form.presentation_date = this.presentation_date;
            this.form.presentation_time = this.presentation_time;

            router.post("/remarks", this.form);
            this.remarks = "";
            this.$bvModal.hide("remarks-modal");
        },
        selectedLead(data) {
            this.selected_row_id = data.id;
            this.remarks = data.assigned_employee.remarks;
            this.lead_status = data.assigned_employee.lead_status;
            this.venue_id = data.venue_id;
            this.updated_by =
                data.updated_by.length != 0
                    ? data.updated_by.last_name +
                      ", " +
                      data.updated_by.first_name
                    : "";
            this.presentation_date = data.presentation_date;
            this.presentation_time = data.presentation_time;
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
        done() {
            this.done_form.lead_id = this.selected_row_id;
            router.post("/done", this.done_form);
            this.$bvModal.hide("done-modal");
        },
        formatDate(value) {
            let date_value = new Date(value);

            return (
                date_value.toLocaleDateString("en-US") +
                " " +
                date_value.toLocaleTimeString("en-US")
            );
        },
    },
};
</script>
