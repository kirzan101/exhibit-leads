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
                :items="itemList"
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
                <template #cell(actions)="row">
                    <Link v-if="check_access('leads', 'read')" :href="module + '/' + row.item.id" class="btn mx-1 my-1 btn-info" type="button">Show</Link>
                    <Link v-if="check_access('leads', 'update')" :href="module + '/' + row.item.id + '/edit'" class="btn mx-1 my-1 btn-warning text-white" type="button">Edit</Link>
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
        </b-container>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue2";

export default {
    components: {
        Link,
    },
    props: {
        items: Array,
        fields: Array,
        per_page: Number,
        module: String
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
            filterOn: ['is_assigned'],
            infoModal: {
                id: "info-modal",
                title: "",
                content: "",
            },
            selected_ids: [],
            checkedAll: false,
            additional_filter: {
                occupation: null
            },
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
        itemList() {
            // add additional filter here
            return this.items;
        },
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.itemList.length;
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

            let access = permissions.filter(item => item.module === module).map(element => ({
                module: element.module,
                type: element.type
            }))

            return access.some(item => item.type === type);
        },
    },
};
</script>
