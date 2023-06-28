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
                    <div class="col-sm-6">
                        Member |
                        <Link href="/members/create" class="btn btn-success"
                            >Add</Link
                        >
                    </div>
                    <div class="col-sm-6">
                        <b-button
                            class="btn btn-info"
                            v-b-modal.assign-modal
                            style="float: right;"
                            align-v="end"
                            v-if="selected_member.length > 0"
                            >Assign</b-button
                        >
                        <b-button
                            class="btn btn-info"
                            v-b-modal.assign-modal
                            style="float: right;"
                            align-v="end"
                            disabled
                            v-else
                            >Assign</b-button
                        >
                        <b-modal title="Assign to Employee" id="assign-modal">
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
                {
                    key: "is_assigned",
                    label: "Is Assigned",
                    sortable: false,
                    formatter: (value, key, item) => {
                        return value ? "Yes" : "No";
                    },
                },
                { key: 'actions', label: 'Actions' }
            ],
            selected_employee: "",
            selected_member: [],
            form: {
                employee_id: "",
                member_ids: [],
            },
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
        submitAssigned() {
            this.form.employee_id = this.selected_employee;
            this.form.member_ids = this.selected_member;

            this.$bvModal.hide("assign-modal");

            router.post("/assign-employee", this.form);
            this.selected_employee = "";
            this.selected_member = [];
        },
    },
};
</script>
