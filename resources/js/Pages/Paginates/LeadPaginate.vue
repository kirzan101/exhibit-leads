<template>
    <div>
        <Head>
            <title>Leads Paginate</title>
        </Head>
        <!--Alert message here start-->
        <b-alert
            v-if="$page.props.flash.success"
            variant="success"
            dismissible
            fade
            :show="showAlert($page.props.flash.success)"
            @dismissed="clearNotif"
        >
            {{ $page.props.flash.success }}
        </b-alert>
        <b-alert
            v-if="$page.props.flash.error"
            variant="danger"
            dismissible
            fade
            :show="showAlert($page.props.flash.error)"
            @dismissed="clearNotif"
        >
            {{ $page.props.flash.error }}
        </b-alert>
        <!--Alert message here end-->

        <b-container fluid>
            <h5>
                <div class="row">
                    <div class="col-sm-6">
                        Leads |
                        <Link
                            href="/leads/create"
                            class="btn btn-success"
                            v-if="check_access('venues', 'create')"
                            >Add</Link
                        >
                    </div>
                    <div class="col-sm-6">&nbsp;</div>
                </div>
            </h5>

            {{ items.current_page }}
            <br />

            <LeadPaginateTable
                :items="items"
                :fields="fields"
                :sort_by="sortBy"
                :sort_desc="sortDesc"
                :search_filter="search"
                :isBusy="is_busy"
                :module="module"
                @toggle-load-data="loadData"
                @selected-ids="getSelectedIds"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import GenericPaginateTable from "../../Components/PaginateTable/GenericPaginateTable.vue";
import LeadPaginateTable from "../../Components/PaginateTable/LeadPaginateTable.vue"

export default {
    components: {
        Link,
        GenericPaginateTable,
        LeadPaginateTable
    },
    props: {
        items: Object,
        sortBy: String,
        sortDesc: Boolean,
        search: String,
    },
    data() {
        return {
            fields: [
                { key: "selected", label: "selected", sortable: false },
                { key: "lead_full_name", label: "Name", isSortable: true },
                { key: "occupation", label: "Occupation", isSortable: true },
                { key: "mobile_number", label: "Mobile No." },
                { key: "source_complete", label: "Source" },
                { key: "venue.name", label: "Venue" },
                { key: "actions", label: "Actions" },
            ],
            alert: false,
            module: "leads",
            is_busy: false,
            selected_ids: []
        };
    },
    methods: {
        updatedPerPage(value) {
            this.per_page = value;
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
        showAlert(data) {
            this.alert = data ? true : false;

            return this.alert;
        },
        clearNotif() {
            this.$page.props.flash.success = null;
            this.$page.props.flash.error = null;
            this.$page.props.flash.message = null;
        },
        loadData(filter) {
            router.reload({
                data: filter,
                only: ["items", "sortBy", "sortDesc", "search"],
            });
        },
        getSelectedIds(data) {
            this.selected_ids = data;
        },
    },
};
</script>
