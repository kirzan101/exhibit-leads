<template>
    <div>
        <Head>
            <title>Venues</title>
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
                        Venues |
                        <Link
                            href="/venues/create"
                            class="btn btn-success"
                            v-if="check_access('venues', 'create')"
                            >Add</Link
                        >
                    </div>
                    <div class="col-sm-6">&nbsp;</div>
                </div>
            </h5>

            <GenericPaginateTable
                :items="items"
                :fields="fields"
                :sort_by="sortBy"
                :sort_desc="sortDesc"
                :search_filter="search"
                :isBusy="is_busy"
                :module="module"
                @toggle-load-data="loadData"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import GenericPaginateTable from "../../Components/PaginateTable/GenericPaginateTable.vue";

export default {
    components: {
        Link,
        GenericPaginateTable,
    },
    props: {
        sortBy: String,
        sortDesc: Boolean,
        search: String,
        module: String,
        items: Object,
    },
    data() {
        return {
            fields: [
                { key: "row_count", label: "row_count", sortable: false },
                {
                    key: "name",
                    label: "Name",
                    isSortable: true,
                },
                { key: "code", label: "Code", isSortable: true },
                { key: "actions", label: "Actions" },
            ],
            alert: false,
            is_busy: false,
        };
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
    },
};
</script>
