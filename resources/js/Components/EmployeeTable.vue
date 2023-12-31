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
                    <!-- <b-form-group
                        label="Is Assigned"
                        label-for="initial-sort-select"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        class="mb-0"
                    >
                        <b-form-select
                            id="initial-sort-select"
                            v-model="filterOn"
                            :options="['all', true, false]"
                            size="sm"
                        ></b-form-select>
                    </b-form-group> -->
                    &nbsp;
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
                :items="items"
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
                <template #cell(name)="row">
                    {{ row.value.first }} {{ row.value.last }}
                </template>

                <template #cell(actions)="row">
                    <!-- <b-button variant="info" class="mx-1">Show</b-button> -->
                    <!-- <b-button variant="warning" class="mx-1">Edit</b-button> -->
                    <Link
                        :href="'employees/' + row.item.id + ''"
                        class="btn mx-1 btn-info"
                        type="button"
                        v-if="check_access('employees', 'read')"
                        >Show</Link
                    >
                    <Link
                        :href="'employees/' + row.item.id + '/edit'"
                        class="btn mx-1 btn-warning text-white"
                        type="button"
                        v-if="check_access('employees', 'update')"
                        >Edit</Link
                    >
                    <b-button
                        variant="danger"
                        class="mx-1"
                        @click="selectedEmployee(row.item)"
                        v-b-modal.reset-modal
                        >Reset</b-button
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

            <!-- Reset modal -->
            <b-modal id="reset-modal" title="Notice">
                <p class="my-4">Reset employee password?</p>
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
                        @click="resetEmployee()"
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
        items: Array,
        fields: Array,
        per_page: Number,
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
            checkedAll: false,
            selected_row_id: "",
        };
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
        selectedEmployee(data) {
            this.selected_row_id = data.id;
        },
        resetEmployee() {
            this.$bvModal.hide("reset-modal");

            if(this.selected_row_id != "") {
                router.post(`/employees/reset-password/${this.selected_row_id}`);
            }

            this.selected_row_id = "";
        },
    },
};
</script>
