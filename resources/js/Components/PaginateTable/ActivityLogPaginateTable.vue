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
    },
};
</script>
