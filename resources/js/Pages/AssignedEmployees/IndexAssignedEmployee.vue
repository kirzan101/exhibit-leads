<template>
    <div>
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
                <div class="row">
                    <div class="col-sm-6">Assigned Members</div>
                    <div class="col-sm-6">
                        <div v-if="selected_member.length > 0">
                            <div v-if="check_access('assigns', 'update')">
                                <b-button
                                class="btn btn-danger mx-1 my-1"
                                v-b-modal.remove-modal
                                style="float: right"
                                align-v="end"
                                >Remove</b-button
                            >
                            <b-button
                                class="btn btn-info mx-1 my-1"
                                v-b-modal.assign-modal
                                style="float: right"
                                align-v="end"
                                >Re-assign</b-button
                            >
                            </div>
                        </div>
                        <div v-else>
                            <b-button
                                class="btn btn-danger mx-1 my-1"
                                style="float: right"
                                align-v="end"
                                disabled
                                >Remove</b-button
                            >
                            <b-button
                                class="btn btn-info mx-1 my-1"
                                style="float: right"
                                align-v="end"
                                disabled
                                >Re-assign</b-button
                            >
                        </div>
                        <!-- Reassign modal start -->
                        <b-modal title="Reassign to Employee" id="assign-modal">
                            <b-form @submit.prevent="submitAssigned">
                                <b-form-select
                                    v-model="selected_employee"
                                    :options="employeeList"
                                    size="sm"
                                    class="mt-3"
                                ></b-form-select>
                            </b-form>
                            <template #modal-footer>
                                <b-button
                                    variant="danger"
                                    type="button"
                                    @click="$bvModal.hide('assign-modal')"
                                    >Close</b-button
                                >
                                <b-button
                                    variant="success"
                                    type="button"
                                    @click="submitAssigned()"
                                    v-if="selected_employee != ''"
                                    >Submit</b-button
                                >
                                <b-button
                                    variant="success"
                                    type="button"
                                    disabled
                                    v-else
                                    >Submit</b-button
                                >
                            </template>
                        </b-modal>
                        <!-- Reassign modal end -->

                        <!-- Remove modal start -->
                        <b-modal title="Notice:" id="remove-modal">
                            <h5>Remove Assigned?</h5>
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
                                    @click="submitRemove()"
                                    >Submit</b-button
                                >
                            </template>
                        </b-modal>
                        <!-- Remove modal End -->
                    </div>
                </div>
            </h5>

            <br />

            <AssignedEmployeeTable
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
import AssignedEmployeeTable from "../../Components/AssignedEmployeeTable.vue";
import MemberTable from "../../Components/MemberTable.vue";

export default {
    components: {
        Link,
        MemberTable,
        AssignedEmployeeTable,
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
                {
                    key: "member_full_name",
                    label: "Member name",
                    sortable: true,
                },
                { key: "address", label: "Address", sortable: true },
                {
                    key: "employee_full_name",
                    label: "Assigned To",
                    sortable: true,
                },
                { key: "actions", label: "Actions" },
            ],
            selected_employee: "",
            selected_member: [],
            selected_member_employee_id: [],
            form: {
                employee_id: "",
                member_ids: [],
            },
            remarks: "",
            members_selected: [],
            checked_all: false,
        };
    },
    watch: {
        showDismissibleAlert() {
            this.$page.props.flash.error = "";
            this.$page.props.flash.success = "";
            this.$page.props.flash.message = "";
        },
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
        getSelectedEmployee(data) {
            this.selected_member_employee_id = data;
        },
        submitAssigned() {
            this.form.employee_id = this.selected_employee;
            this.form.member_ids = this.selected_member;

            this.$bvModal.hide("assign-modal");

            router.post("/reassign-employee", this.form);
            this.selected_employee = "";
            this.selected_member = [];
        },
        submitRemove() {
            this.form.member_ids = this.selected_member;

            this.$bvModal.hide("remove-modal");
            
            router.post("/remove-assign", this.form);
            this.selected_employee = "";
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
    },
};
</script>
