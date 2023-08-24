<template>
    <b-container fluid>
        <!-- User Interface controls -->
        <b-row>
            <b-col lg="6" class="my-1">
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
                            type="search"
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
                        v-model.lazy="filter.per_page"
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
                ></b-form-checkbox>
            </template>

            <template v-slot:cell(selected)="row">
                <b-form-group>
                    <b-form-checkbox
                        v-model="selected_ids"
                        :value="row.item.id"
                        :id="row.item.id + '-id'"
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
                <Link
                    :href="'/' + module + '/' + row.item.id + '/edit'"
                    class="btn m-1 btn-warning text-white"
                    type="button"
                    v-if="check_access(module, 'update')"
                    >Edit</Link
                >
            </template>
            <!-- actions end -->
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
            },
            selected_ids: [],
            checkedAll: false,
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
        currentPage() {
            this.filterTable();
        },
        search() {
            // this.filterTable();
        },
        selected_ids() {
            return this.$emit("selected-ids", this.selected_ids);
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
            this.filter.page = this.currentPage;
            this.filter.sort_by = this.sortBy;
            this.filter.is_sort_desc = this.sortDesc;
            this.filter.per_page = this.perPage;
            this.filter.search = this.search;
            this.is_Busy = true;

            this.$emit("toggle-load-data", this.filter);
        },
        selectAll() {
            this.selected_ids = [];
            if (this.checkedAll) {
                for (let i in this.items.data) {
                    this.selected_ids.push(this.items.data[i].id);
                }
            }
        },
        select(item) {
            if (this.selected_ids.includes(item.id)) {
                const index = this.selected_ids.indexOf(item.id);
                if (index > -1) {
                    this.selected_ids.splice(index, 1);
                }
            } else {
                this.selected_ids.push(item.id);
            }
        },
    },
};
</script>
