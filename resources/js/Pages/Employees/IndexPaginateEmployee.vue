<template>
    <div>
        <Head>
            <title>Employees</title>
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
                        Employees |
                        <Link
                            href="/employees/create"
                            class="btn btn-success"
                            v-if="check_access('employees', 'create')"
                            >Add</Link
                        >
                    </div>
                    <div class="col-sm-6">&nbsp;</div>
                </div>
            </h5>

            <EmployeePaginateTable
                :items="items"
                :fields="fields"
                :sort_by="sortBy"
                :sort_desc="sortDesc"
                :search_filter="search"
                :isBusy="is_busy"
                :module="module"
                :user_groups="user_groups"
                :positions="positions"
                :user_group_id="user_group_id"
                :position="position"
                @toggle-load-data="loadData"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import EmployeePaginateTable from "../../Components/PaginateTable/EmployeePaginateTable.vue";

export default {
    components: {
        Link,
        EmployeePaginateTable,
    },
    props: {
        sortBy: String,
        sortDesc: Boolean,
        search: String,
        module: String,
        items: Object,
        user_groups: Array,
        positions: Array,
        user_group_id: Number,
        position: String
    },
    data() {
        return {
            fields: [
                { key: "row_count", label: "row_count", sortable: false },
                { key: "full_name", label: "Name", isSortable: true },
                { key: "user.email", label: "Email", isSortable: false },
                { key: "position", label: "Position", isSortable: false },
                { key: "user_group.name", label: "User Group", isSortable: false },
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
                only: ["items", "sortBy", "sortDesc", "search", "user_group_id", "position"],
            });
        },
    },
};
</script>
