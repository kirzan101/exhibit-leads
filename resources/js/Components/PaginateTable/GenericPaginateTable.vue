<template>
    <div class="overflow-auto">
        <!-- 
        <b-pagination-nav
            :link-gen="linkGen"
            :number-of-pages="last_page"
            :total-rows="rows"
            :per-page="per_page"
            :current-page="current_page"
            use-router
        ></b-pagination-nav>

        <p class="mt-3">Current Page: {{ currentPage }}</p>

        <b-table striped hover :items="items" :fields="fields"></b-table> -->
        <b-pagination
            v-model="currentPage"
            :total-rows="rows"
            :per-page="per_page"
            aria-controls="my-table"
        ></b-pagination>

        <p class="mt-3">Current Page: {{ currentPage }}</p>
        <b-table
            striped
            hover
            :items="items"
            :fields="fields"
            @sort-changed="sortingChanged"
        ></b-table>

        <!-- <b-table
            id="my-table"
            :link-gen="linkGen"
            :items="items"
            :per-page="per_page"
            :current-page="currentPage"
            :fields="fields"
            small
        ></b-table> -->
    </div>
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
    },
    data() {
        return {
            currentPage: this.current_page,
            perPage: this.per_page,
            sortBy: 'id',
            sortDesc: true,
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
    },
    watch: {
        currentPage(value) {
            this.paginateLink(value);
        },
    },
    methods: {
        linkGen(pageNum) {
            // return pageNum === 1 ? "?" : `?page=${pageNum}`;
            return `/paginate/leads/request?page=${pageNum}`;
        },
        paginateLink(data) {
            // let url = `/paginate/leads/request?page=${data}`;
            let url = '/paginate/leads/request';

            router.visit(url, {
                data: {
                    page: this.currentPage,
                    sortBy: this.sortBy,
                    sortDesc: this.sortDesc
                },
                only: [
                    "items",
                    "per_page",
                    "current_page",
                    "last_page",
                    "total",
                ],
            });
        },
        sortingChanged(ctx) {
            this.sortBy = ctx.sortBy;
            this.sortDesc = ctx.sortDesc;
            console.log(ctx);

            // if name tab is choose, set last name as default
            if(ctx.sortBy === 'lead_full_name') {
                this.sortBy = 'last_name';
            }

            // refresh data
            this.paginateLink();
        },
    },
};
</script>
