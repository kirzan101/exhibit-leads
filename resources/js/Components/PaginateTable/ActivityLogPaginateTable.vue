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

            <!-- booker filter -->
            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="Causer"
                    label-for="causer-select"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-form-select
                        id="causer-select"
                        v-model="filter.user_id"
                        :options="causerOptions"
                        @change="filterTable"
                        size="sm"
                    ></b-form-select>
                </b-form-group>
            </b-col>

            <b-col sm="6" md="6" class="my-1">
                <b-form-group
                    label="Name"
                    label-for="name-input"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                    <b-input-group size="sm">
                        <b-form-input
                            id="name-input"
                            v-model="filter.name"
                            type="name"
                            placeholder="Name"
                            v-debounce:500ms="filterTable"
                            maxlength="15"
                        ></b-form-input>

                        <b-input-group-append>
                            <b-button
                                :disabled="!filter.name"
                                @click="
                                    filter.name = '';
                                    filterTable();
                                "
                                >Clear</b-button
                            >
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>
            <!-- booker filter end-->

            <!-- access date filter start -->
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
                        @change="filterDate"
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
                        @change="filterDate"
                    ></b-form-input>
                </b-form-group>
            </b-col>
            <!-- access date filter end -->

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
        >
            <!-- loader start -->
            <template #table-busy>
                <div class="text-center text-info my-2">
                    <b-spinner class="align-middle mr-2"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
            <!-- loader end -->

            <!-- browser start -->
            <template #cell(browser)="row">
                <b-button
                    variant="primary"
                    v-b-modal.modal-json
                    @click="viewJson(row.item.browser, 'Browser')"
                    >View</b-button
                >
            </template>
            <!-- browser end -->

            <!-- properties start -->
            <template #cell(properties)="row">
                <b-button
                    variant="success"
                    v-b-modal.modal-json
                    @click="viewJson(row.item.properties, 'Properties')"
                    >View</b-button
                >
            </template>
            <!-- properties end -->

            <!-- format assigned date start -->
            <template #cell(created_at)="row">
                {{ formatDate(row.item.created_at) }}
            </template>
            <!-- format assigned date end -->

            <!-- row count start -->
            <template #head(row_count)="column"> # </template>

            <template v-slot:cell(row_count)="row">
                {{ rowNumbering(row.index) }}
            </template>
            <!-- row count end -->
        </b-table>

        <JsonViewModal
            v-if="jsonModal.jsonData || jsonModal.title"
            :title="jsonModal.title"
            :jsonData="jsonModal.jsonData"
        />
    </b-container>
</template>

<script>
import { Link } from "@inertiajs/vue2";
import JsonViewModal from "../Modals/JsonViewModal.vue";

export default {
    props: {
        items: Object,
        fields: Array,
        sort_by: String,
        sort_desc: Boolean,
        search_filter: String,
        module: String,
        isBusy: Boolean,
        startTo: String,
        endTo: String,
        nameInput: String,
        userId: String,
        causer_list: Array
    },
    components: {
        Link,
        JsonViewModal,
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
                start_to: this.startTo,
                end_to: this.endTo,
                name: this.nameInput,
                user_id: this.userId
            },
            jsonModal: {
                title: null,
                jsonData: null,
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
        causerOptions() {
            return [
                { text: "-- select --", value: null },
                ...this.causer_list
                    .map((f) => {
                        return { text: f.full_name, value: f.user.id };
                    }),
            ];
        },
    },
    watch: {
        "filter.page": function () {
            this.filterTable();
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
        formatDate(value) {
            let date_value = new Date(value);

            return (
                date_value.toLocaleDateString("en-US") +
                " " +
                date_value.toLocaleTimeString("en-US")
            );
        },
        viewJson(data, title) {
            this.jsonModal.jsonData = data;
            this.jsonModal.title = title;
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
    },
};
</script>
