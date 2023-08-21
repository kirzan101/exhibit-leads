<template>
    <b-container fluid>
        <!-- User Interface controls -->
        <b-row>
            <!-- first filter start -->
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
                            v-model="sortBy"
                            :options="sortOptions"
                            :aria-describedby="ariaDescribedby"
                            class="w-75"
                            @change="filterTable"
                        >
                        </b-form-select>

                        <b-form-select
                            v-model="sortDesc"
                            :disabled="!sortBy"
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
                            v-model.lazy="search"
                            type="search"
                            placeholder="Type to Search"
                            v-debounce:1000ms="filterTable"
                            maxlength="15"
                        ></b-form-input>

                        <b-input-group-append>
                            <b-button :disabled="!search" @click="search = ''"
                                >Clear</b-button
                            >
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>
            <!-- first filter end -->

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
                        @change="filterTable"
                    ></b-form-select>
                </b-form-group>
            </b-col>

            <b-col sm="7" md="6" class="my-1">
                <b-pagination
                    v-model="currentPage"
                    :total-rows="rows"
                    :per-page="perPage"
                    align="fill"
                    size="sm"
                    class="my-0"
                ></b-pagination>
            </b-col>
        </b-row>

        <i
            >Showing <b>{{ filteredRows }}</b> of <b>{{ rows }}</b></i
        >
        <!-- Main table element -->
        <b-table
            :items="items.data"
            :fields="fields"
            :current-page="currentPage"
            :busy="isBusy"
            stacked="md"
            show-empty
            small
            :no-local-sorting="true"
        >
            <template #table-busy>
                <div class="text-center text-info my-2">
                    <b-spinner class="align-middle mr-2"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
        </b-table>
    </b-container>
</template>

<script>
export default {
    props: {
        fields: Array,
        module: String,
        sort_by: String,
        sort_desc: Boolean,
        search_filter: String,
        items: Object,
    },
    data() {
        return {
            currentPage: this.items.meta.current_page,
            perPage: this.items.meta.per_page,
            sortBy: this.sort_by,
            sortDesc: this.sort_desc,
            sortDirection: "asc",
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
            isBusy: false,
            search: this.search_filter,
            filter: {
                page: 1,
                sortBy: null,
                sortDesc: null,
                per_page: 5,
                search: null,
            },
        };
    },
    computed: {
        rows() {
            return this.items.meta.total;
        },
        filteredRows() {
            return this.items.data.length;
        },
        currentPagedata() {
            return this.items.meta.current_page;
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
    },
    methods: {
        filterTable() {
            this.filter.page = this.currentPage;
            this.filter.sortBy = this.sortBy;
            this.filter.sortDesc = this.sortDesc;
            this.filter.per_page = this.perPage;
            this.filter.search = this.search;
            this.isBusy = true;

            this.$emit("toggle-paginate-link", this.filter);
        },
    },
};
</script>
