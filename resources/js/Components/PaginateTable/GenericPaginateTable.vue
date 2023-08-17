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
                            v-model="sortBy"
                            :options="sortOptions"
                            :aria-describedby="ariaDescribedby"
                            class="w-75"
                            @change="sortingChanged"
                        >
                            <template #first>
                                <option value="">-- none --</option>
                            </template>
                        </b-form-select>

                        <b-form-select
                            v-model="sortDesc"
                            :disabled="!sortBy"
                            :aria-describedby="ariaDescribedby"
                            size="sm"
                            class="w-25"
                            @change="sortingChanged"
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
                            v-model="search"
                            type="search"
                            placeholder="Type to Search"
                        ></b-form-input>

                        <b-input-group-append>
                            <b-button :disabled="!search" @click="search = ''"
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
                        v-model="perPage"
                        :options="pageOptions"
                        size="sm"
                        @change="perPageChange"
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

        <!-- Main table element -->
        <b-table
            :items="items"
            :fields="fields"
            :current-page="currentPage"
            :busy="isBusy"
            stacked="md"
            show-empty
            small
            :no-local-sorting="true"
        >
            <template #table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
        </b-table>
    </b-container>
</template>

<script>
import { router } from "@inertiajs/vue2";

export default {
    props: {
        items: Array,
        per_page: Number,
        current_page: Number,
        fields: Array,
        last_page: Number,
        total: Number,
        module: String,
    },
    data() {
        return {
            currentPage: this.current_page,
            perPage: this.per_page,
            sortBy: '',
            sortDesc: true,
            sortDirection: "asc",
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
            isBusy: false,
            search: null,
            filter: {
                page: 1,
                sortBy: '',
                sortDesc: false,
                per_page: 5,
                search: null
            },
        };
    },
    computed: {
        rows() {
            // return this.items.length;
            return this.total;
        },
        currentPagedata() {
            return (this.currentPage = this.current_page);
        },
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter((f) => f.isSortable)
                .map((f) => {
                    return { text: f.label, value: f.key };
                });
        },
    },
    watch: {
        currentPage(value) {
            this.paginateLink(value);
        },
    },
    methods: {
        linkGen(pageNum) {
            return `/paginate/leads/request?page=${pageNum}`;
        },
        paginateLink() {
            this.filter.page = this.currentPage;
            this.filter.sortBy = this.sortBy;
            this.filter.sortDesc = this.sortDesc;
            this.filter.per_page = this.perPage;

            this.$emit("toggle-paginate-link", this.filter);
        },
        sortingChanged() {
            if (this.sortBy === "lead_full_name") {
                this.sortBy = "last_name";
            }

            console.log(this.sortBy);

            // refresh data
            this.paginateLink();
        },
        perPageChange() {
            this.paginateLink();
        },
    },
};
</script>
