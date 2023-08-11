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
                        label="Occupation"
                        label-for="occupation-select"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                    >
                        <b-form-select
                            id="occupation-select"
                            v-model="additional_filter.occupation"
                            :options="occupation_options"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>

                <!-- second filter -->
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
                            v-model="additional_filter.venue_id"
                            :options="venue_options"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>

                <b-col lg="6" class="my-1">
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
                            v-model="additional_filter.source_name"
                            :options="source_options"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>

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
                :items="leadList"
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
                        v-if="check_access('confirms', 'create')"
                    ></b-form-checkbox>
                </template>

                <template v-slot:cell(selected)="row">
                    <b-form-group>
                        <b-form-checkbox
                            v-model="selected_ids"
                            :value="row.item.id"
                            :id="row.item.id + '-' + row.item.last_name"
                            :disabled-field="row.item.is_done"
                            v-if="check_access('confirms', 'create')"
                        ></b-form-checkbox>
                    </b-form-group>
                </template>

                <template v-slot:cell(lead_status)="row">
                    <b-button
                        v-b-modal.employee-remarks-modal
                        variant="primary text-white"
                        class="m-1"
                        @click="selected_lead(row.item)"
                        >{{ row.item.assigned_employee.lead_status }}</b-button
                    >
                </template>

                <template #cell(actions)="row">
                    <Link
                        :href="'assigned-confirmers/' + row.item.id"
                        class="btn m-1 btn-info"
                        type="button"
                        v-if="check_access('confirms', 'read')"
                        >Show</Link
                    >
                    <!-- 
                    <b-button
                        v-b-modal.employee-remarks-modal
                        variant="warning text-white"
                        class="m-1"
                        @click="selected_lead(row.item)"
                        >Remarks</b-button
                    > -->

                    <b-button
                        v-b-modal.confirm-modal
                        variant="success text-white"
                        @click="selected_lead(row.item)"
                        class="m-1"
                        v-if="!row.item.is_confirm_assigned"
                        >Confirm</b-button
                    >
                    <b-button
                        v-b-modal.confirm-modal
                        variant="danger text-white"
                        @click="selected_lead(row.item)"
                        class="m-1"
                        v-else
                        >Edit confirm</b-button
                    >

                    <b-button
                        v-b-modal.done-modal
                        variant="success text-white"
                        class="m-1"
                        v-if="row.item.is_confirm_assigned"
                        @click="selected_lead(row.item)"
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

            <!-- Info modal -->
            <b-modal
                :id="infoModal.id"
                :title="infoModal.title"
                ok-only
                @hide="resetInfoModal"
            >
                <pre>{{ infoModal.content }}</pre>
            </b-modal>

            <!-- Employee Remarks modal -->
            <b-modal id="employee-remarks-modal" title="Booker Remarks">
                <b-form-textarea
                    id="textarea"
                    placeholder="Enter something..."
                    rows="3"
                    max-rows="6"
                    readonly
                    v-model="employee_remarks"
                ></b-form-textarea>
                <p class="mt-2 mb-2">Booker Lead status:</p>
                <b-form-select
                    :disabled="true"
                    :value="employee_lead_status"
                    :options="lead_status_options"
                ></b-form-select>
                <p class="mt-4 mb-2">
                    Remarks by: <b>{{ employee_name }}</b>
                </p>

                <template #modal-footer>
                    <b-button
                        variant="danger"
                        type="button"
                        @click="$bvModal.hide('employee-remarks-modal')"
                        >Close</b-button
                    >
                </template>
            </b-modal>

            <!-- Confirm modal -->
            <b-modal id="confirm-modal" title="Confirm Lead:">
                <p class="mt-2 mb-2">Remarks:</p>
                <b-form-textarea
                    id="textarea"
                    placeholder="Enter something..."
                    rows="3"
                    max-rows="6"
                    v-model="remarks"
                ></b-form-textarea>
                <p class="mt-2 mb-2">Lead status:</p>
                <b-form-select
                    v-model="lead_status"
                    :options="lead_status_confirmer_options"
                ></b-form-select>
                <p v-if="confirmer_name" class="mt-4 mb-2">
                    Last remarks by: <b>{{ confirmer_name }}</b>
                </p>

                <template #modal-footer>
                    <b-button
                        variant="danger"
                        type="button"
                        @click="$bvModal.hide('confirm-modal')"
                        >Close</b-button
                    >
                    <b-button
                        variant="success"
                        type="button"
                        @click="submitConfirm"
                        >Save</b-button
                    >
                </template>
            </b-modal>

            <!-- Done modal -->
            <b-modal id="done-modal" title="Notice:">
                <p>Mark as done?</p>

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
        occupation_list: Array,
        status_list: Array,
        confirmer_status_list: Array,
        venues: Array,
        sources: Array,
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
            filterOn: ["is_done"],
            infoModal: {
                id: "info-modal",
                title: "",
                content: "",
            },
            selected_ids: [],
            checkedAll: false,
            employee_remarks: "",
            employee_lead_status: "",
            employee_name: "",
            remarks: "",
            lead_status: "",
            confirmer_name: "",
            has_confirmer: false,
            form: {
                lead_status: null,
                remarks: null,
                lead_id: null,
            },
            done_form: {
                status: true,
                lead_id: "",
                employee_type: "confirmer",
            },
            additional_filter: {
                occupation: null,
                venue_id: null,
                source_name: null,
            },
            occupation_options: [
                { value: null, text: "-- select --" },
                ...this.occupation_list.map((item) => {
                    return { value: item.occupation, text: item.occupation };
                }),
            ],
            lead_status_options: [
                { value: null, text: "-- select --" },
                ...this.status_list.map((item) => {
                    return {
                        value: item.name,
                        text: item.name,
                    };
                }),
            ],
            lead_status_confirmer_options: [
                { value: null, text: "-- select --" },
                ...this.confirmer_status_list.map((item) => {
                    return {
                        value: item.name,
                        text: item.name,
                    };
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
        };
    },
    watch: {
        selected_ids() {
            return this.$emit("selected_lead", this.selected_ids);
        },
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter((f) => f.sortable)
                .map((f) => {
                    return { text: f.label, value: f.key };
                });
        },
        leadList() {
            // filter property
            if (this.additional_filter.occupation) {
                return this.items.filter(
                    (item) =>
                        item.occupation == this.additional_filter.occupation
                );
            }

            if (this.additional_filter.venue_id) {
                return this.items.filter(
                    (item) => item.venue_id == this.additional_filter.venue_id
                );
            }

            if (this.additional_filter.source_name) {
                return this.items.filter(
                    (item) =>
                        item.source_complete ==
                        this.additional_filter.source_name
                );
            }

            return this.items;
        },
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.leadList.length;
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
            if (this.checkedAll) {
                for (let i in this.leadList
                    .filter((item) => item.is_done == false)
                    .slice(0, this.perPage)) {
                    this.selected_ids.push(this.items[i].id);
                }
            }
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
        selected_lead(data) {
            // get the data of selected lead

            // set employee remarks & lead status
            this.employee_remarks = data.assigned_employee.remarks;
            this.employee_lead_status = data.assigned_employee.lead_status;
            this.employee_name = data.assigned_employee.updatedBy.full_name;

            // set confirmer remarks & lead status
            this.remarks = data.assigned_confirmer
                ? data.assigned_confirmer.remarks
                : null;
            this.lead_status = data.assigned_confirmer
                ? data.assigned_confirmer.lead_status
                : null;
            this.has_confirmer = data.assigned_confirmer ? true : false;
            this.confirmer_name = "";

            //set current lead
            this.form.lead_id = data.id;
            this.done_form.lead_id = data.id;
        },
        submitConfirm() {
            this.form.lead_status = this.lead_status;
            this.form.remarks = this.remarks;

            router.post("/confirm", this.form);

            //clear form contents
            this.form.lead_status = "";
            this.form.remarks = "";
            this.form.lead_id = "";
            this.$bvModal.hide("confirm-modal");
        },
        done() {
            router.post("/done", this.done_form);
            this.done_form.lead_id = "";
            this.$bvModal.hide("done-modal");
        },
    },
};
</script>
