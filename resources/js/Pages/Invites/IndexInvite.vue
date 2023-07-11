<template>
    <div>
        <Head>
            <title>Invited</title>
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
                        Invited 
                    </div>
                    <div class="col-sm-6">
                        <div v-if="check_access('invites', 'create')">
                            <b-button
                                class="btn btn-danger"
                                v-b-modal.remove-modal
                                style="float: right"
                                align-v="end"
                                v-if="selected_member.length > 0"
                                >Remove</b-button
                            >
                            <b-button
                                class="btn btn-danger"
                                v-b-modal.remove-modal
                                style="float: right"
                                align-v="end"
                                disabled
                                v-else
                                >Remove</b-button
                            >
                        </div>
                        <b-modal title="Assign to Employee" id="remove-modal">
                            <p>Remove invite remarks?</p>
                            <template #modal-footer>
                                <b-button
                                    variant="danger"
                                    type="button"
                                    @click="$bvModal.hide('remove-modal')"
                                    >Close</b-button
                                >
                                <b-button
                                    variant="success"
                                    type="button"
                                    @click="submitInvite()"
                                    v-if="selected_employee != ''"
                                    >Yes</b-button
                                >
                                <b-button
                                    variant="success"
                                    type="button"
                                    disabled
                                    v-else
                                    >Yes</b-button
                                >
                            </template>
                        </b-modal>
                    </div>
                </div>
            </h5>

            <br />

            <MemberTable
                :fields="fields"
                :items="members"
                :per_page="per_page"
                @selected_member="getSelectedMember($event)"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import MemberTable from "../../Components/MemberTable.vue";

export default {
    components: {
        Link,
        MemberTable,
    },
    props: {
        members: Array,
        employees: Array,
        per_page: Number,
    },
    data() {
        return {
            showDismissibleAlert: false,
            fields: [
                { key: "selected", label: "selected", sortable: false },
                {
                    key: "id",
                    label: "Id",
                    sortable: true,
                    sortDirection: "desc",
                },
                { key: "first_name", label: "First name", sortable: true },
                { key: "last_name", label: "Last name", sortable: true },
                { key: "address", label: "Address", sortable: true },
                // {
                //     key: "is_assigned",
                //     label: "Is Assigned",
                //     sortable: false,
                //     formatter: (value, key, item) => {
                //         return value ? "Yes" : "No";
                //     },
                // },
                // { key: "actions", label: "Actions" },
                {
                    key: "employee_full_name",
                    label: "Assigned To",
                    sortable: true,
                },
            ],
            selected_member: [],
            form: {
                member_ids: [],
            },
            alert: false,
        };
    },
    computed: {
        employeeList() {
            return this.employees.map((employee) => {
                return {
                    value: employee.id,
                    text: employee.last_name + ", " + employee.first_name,
                };
            });
        },
    },
    methods: {
        updatedPerPage(value) {
            this.per_page = value;
        },
        getSelectedMember(data) {
            this.selected_member = data;
        },
        submitInvite() {
            this.form.member_ids = this.selected_member;

            this.$bvModal.hide("remove-modal");

            router.post("/invites/cancel", this.form);
            this.selected_member = [];
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
