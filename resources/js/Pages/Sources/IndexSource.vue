<template>
    <div>
        <Head>
            <title>Sources</title>
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
                        Sources |
                        <Link
                            href="/sources/create"
                            class="btn btn-success"
                            v-if="check_access('sources', 'create')"
                            >Add</Link
                        >
                    </div>
                    <div class="col-sm-6">
                        &nbsp;
                    </div>
                </div>
            </h5>

            <br />

            <GenericTable
                :fields="fields"
                :items="sources"
                :per_page="per_page"
                :module="module"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import GenericTable from "../../Components/GenericTable.vue";

export default {
    components: {
        Link,
        GenericTable,
    },
    props: {
        sources: Array,
        employees: Array,
        per_page: Number,
    },
    data() {
        return {
            showDismissibleAlert: false,
            fields: [
                { key: "name", label: "Name", sortable: true },
                { key: "description", label: "Description", sortable: false },
                { key: "actions", label: "Actions" },
            ],
            alert: false,
            module: 'sources',
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
