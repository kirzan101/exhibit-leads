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
                        label="Assigned to"
                        label-for="exhibitors-select"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                    >
                        <b-form-select
                            id="exhibitors-select"
                            v-model="exhibitor_id"
                            :options="exhibitor_options"
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
                            :disabled-field="row.item.is_exhibitor_assigned"
                            @change="
                                selectEmployee($event, row.item.assigned_exhibitor.employee_id)
                            "
                            v-if="check_access('assigns', 'create')"
                        ></b-form-checkbox>
                    </b-form-group>
                </template>

                <template #cell(name)="row">
                    {{ row.value.first }} {{ row.value.last }}
                </template>

                <template #cell(assigned_exhibitor.created_at)="row">
                    {{ formatDate(row.item.assigned_exhibitor.created_at) }}
                </template>

                <template #cell(actions)="row">
                    <Link
                        :href="'assigned-exhibitors/' + row.item.id"
                        class="btn m-1 btn-info"
                        type="button"
                        v-if="check_access('assign-exhibitors', 'read')"
                        >Show</Link
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
        employees: Array,
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
            filterOn: ["is_exhibitor_assigned"],
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
            updated_by: "",
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
            exhibitor_options: [
                { value: null, text: "-- select --" },
                ...this.employees.map((exhibitor) => {
                    return {
                        value: exhibitor.id,
                        text: exhibitor.last_name + ", " + exhibitor.first_name,
                    };
                }),
            ],
            exhibitor_id: null,
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
            // assigned date filter
            if (this.start_to != "" && this.end_to != "") {
                return this.items.filter((item) => {
                    const itemDate = new Date(
                        item.assigned_exhibitor.created_at
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

            if (this.exhibitor_id) {
                return this.items.filter((item) => {
                    return item.assigned_exhibitor.employee_id == this.exhibitor_id;
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
                for (let i in this.items
                    .filter((item) => item.is_exhibitor_assigned == true)
                    .slice(0, this.perPage)) {
                    this.selected_ids.push(this.items[i].id);
                    this.selected_employee_ids.push(this.items[i].assigned_exhibitor.employee_id);
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
