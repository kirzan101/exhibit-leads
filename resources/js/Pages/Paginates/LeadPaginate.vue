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
                    <div class="col-sm-6">
                        &nbsp;
                    </div>
                </div>
            </h5>

            <br />
            <GenericPaginateTable :items="items" :per_page="per_page" :fields="fields" :current_page="current_page" :last_page="last_page" :total="total" />
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
        GenericPaginateTable
    },
    props: {
        items: Array,
        per_page: Number,
        current_page: Number,
        last_page: Number,
        total: Number
    },
    data() {
        return {
            fields: [
                { key: "lead_full_name", label: "Name", sortable: true },
                { key: "occupation", label: "Occupation", sortable: true },
                { key: "mobile_number", label: "Mobile No." },
            ],
            alert: false,
            module: 'venues',
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
    },
};
</script>
