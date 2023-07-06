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
            :show="$page.props.flash.success ? true : false"
            @dismissed="showDismissibleAlert = false"
        >
            {{ $page.props.flash.success }}
        </b-alert>
        <b-alert
            v-if="$page.props.flash.error"
            variant="danger"
            dismissible
            fade
            :show="$page.props.flash.error ? true : false"
            @dismissed="showDismissibleAlert = false"
        >
            {{ $page.props.flash.error }}
        </b-alert>
        <!--Alert message here end-->

        <b-container fluid>
            <h5>
                Employee |
                <Link href="/employees/create" class="btn btn-success" v-if="check_access('employees', 'create')"
                    >Add</Link
                >
            </h5>

            <br />

            <EmployeeTable
                :fields="fields"
                :items="employees"
                :per_page="per_page"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router, Head } from "@inertiajs/vue2";
import EmployeeTable from "../../Components/EmployeeTable.vue";

export default {
    components: {
        Link,
        EmployeeTable,
    },
    props: {
        employees: Array,
        per_page: Number,
    },
    data() {
        return {
            showDismissibleAlert: false,
            fields: [
                {
                    key: "id",
                    label: "Id",
                    sortable: true,
                    sortDirection: "desc",
                },
                { key: "first_name", label: "First name", sortable: true },
                { key: "last_name", label: "Last name", sortable: true },
                { key: "position", label: "Position", sortable: true },
                { key: "property", label: "Property", sortable: true },
                { key: "actions", label: "Actions" },
            ],
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
    },
};
</script>
