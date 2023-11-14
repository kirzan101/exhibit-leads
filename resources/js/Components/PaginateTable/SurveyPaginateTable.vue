<template>
    <b-container fluid>
        <!-- User Interface controls -->
        <b-row>
            <!-- first filter -->
            <b-col lg="6" class="my-1"> &nbsp; </b-col>

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

            <!-- second filter -->
            <b-col sm="5" md="6" class="my-1">
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

            <b-col sm="7" md="6" class="my-1">
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

            <!-- third filter -->
            <b-col sm="5" md="6" class="my-1">
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

            <b-col sm="7" md="6" class="my-1">
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

            <b-col sm="5" md="6" class="my-1">
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

            <b-col sm="7" md="6" class="my-1">
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
                    v-if="check_access('surveys', 'create')"
                ></b-form-checkbox>
                <span v-else>&nbsp;</span>
            </template>

            <template v-slot:cell(selected)="row">
                <b-form-group>
                    <b-form-checkbox
                        v-model="selectedIds"
                        :value="row.item.id"
                        :id="row.item.id + '-id'"
                        v-if="check_access('surveys', 'create')"
                    ></b-form-checkbox>
                </b-form-group>
            </template>
            <!-- checkbox end -->

            <!-- actions start -->
            <template #cell(actions)="row">
                <Link
                    :href="'/' + module + '/' + row.item.id"
                    class="btn m-1 btn-info"
                    type="button"
                    v-if="check_access(module, 'read')"
                    >Show</Link
                >
            </template>
            <!-- actions end -->

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
import { Link } from "@inertiajs/vue2";

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
        exhibitors: Array,
        exhibitor: Number,
        occupationName: String,
        venueId: String,
        sourceName: String,
    },
    components: {
        Link,
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
                ...this.fields
                    .filter((f) => f.isSortable)
                    .map((f) => {
                        return { text: f.label, value: f.key };
                    }),
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
        rowNumbering(rowCount) {
            console.log(this.items.meta);

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
