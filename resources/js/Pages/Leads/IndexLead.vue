<template>
    <div>
        <Head>
            <title>Leads</title>
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
                            v-if="check_access('leads', 'create')"
                            >Add</Link
                        >
                    </div>
                    <div class="col-sm-6">
                        <div
                            v-if="
                                user_group == 'exhibit' || user_group == 'admin'
                            "
                        >
                            <!-- Assign Employee -->
                            <div v-if="check_access('assigns', 'create')">
                                <b-button
                                    class="btn btn-info m-1"
                                    v-b-modal.assign-modal
                                    style="float: right"
                                    align-v="end"
                                    v-if="selected_lead.length > 0"
                                    >Assign</b-button
                                >
                                <b-button
                                    class="btn btn-info m-1"
                                    v-b-modal.assign-modal
                                    style="float: right"
                                    align-v="end"
                                    disabled
                                    v-else
                                    >Assign</b-button
                                >
                            </div>
                            <b-modal
                                title="Assign to Employee"
                                id="assign-modal"
                            >
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

                        <div
                            v-if="
                                user_group == 'exhibit-admin' ||
                                user_group == 'admin'
                            "
                        >
                            <!-- Exhibitor assign -->
                            <div
                                v-if="
                                    check_access('assign-exhibitors', 'create')
                                "
                            >
                                <b-button
                                    class="btn btn-info m-1"
                                    v-b-modal.assign-exhibitor-modal
                                    style="float: right"
                                    align-v="end"
                                    v-if="selected_lead.length > 0"
                                    >Assign Exhibitor</b-button
                                >
                                <b-button
                                    class="btn btn-info m-1"
                                    v-b-modal.assign-exhibitor-modal
                                    style="float: right"
                                    align-v="end"
                                    disabled
                                    v-else
                                    >Assign Exhibitor</b-button
                                >
                            </div>
                            <b-modal
                                title="Assign to Exhibitor"
                                id="assign-exhibitor-modal"
                            >
                                <b-form @submit.prevent="submitAssigned">
                                    <b-form-select
                                        v-model="selected_exhibitor"
                                        :options="exhibitorList"
                                        :disabled="true"
                                        size="sm"
                                        class="mt-3"
                                    ></b-form-select>
                                </b-form>
                                <template #modal-footer>
                                    <b-button
                                        variant="danger"
                                        type="button"
                                        @click="
                                            $bvModal.hide(
                                                'assign-exhibitor-modal'
                                            )
                                        "
                                        >Close</b-button
                                    >
                                    <b-button
                                        variant="success"
                                        type="button"
                                        @click="submitAssignedExhibitor()"
                                        v-if="selected_exhibitor != ''"
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
                </div>
            </h5>

            <br />

            <leadTable
                :fields="fields"
                :items="leads"
                :per_page="per_page"
                :occupation_list="occupation_list"
                :venues="venues"
                :sources="sources"
                @selected_lead="getSelectedLead($event)"
            />
        </b-container>

        <br />
    </div>
</template>

<script>
import { Link, router } from "@inertiajs/vue2";
import leadTable from "../../Components/LeadTable.vue";

export default {
    components: {
        Link,
        leadTable,
    },
    props: {
        leads: Array,
        employees: Array,
        per_page: Number,
        occupation_list: Array,
        venues: Array,
        sources: Array,
        exhibitors: Array,
        exhibitor: Number
    },
    data() {
        return {
            showDismissibleAlert: false,
            fields: [
                { key: "selected", label: "selected", sortable: false },
                { key: "first_name", label: "First name", sortable: true },
                { key: "last_name", label: "Last name", sortable: true },
                { key: "occupation", label: "Occupation", sortable: true },
                {
                    key: "mobile_number",
                    label: "Mobile number",
                    sortable: false,
                },
                { key: "source", label: "Source", sortable: true },
                { key: "venue.name", label: "Venue", sortable: true },
                { key: "actions", label: "Actions" },
            ],
            selected_employee: "",
            selected_exhibitor: "",
            selected_lead: [],
            form: {
                employee_id: "",
                lead_ids: [],
            },
            alert: false,
        };
    },
    mounted() {
        this.selected_exhibitor = (this.exhibitor) ? this.exhibitor : '';
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
        exhibitorList() {
            return this.exhibitors.map((employee) => {
                return {
                    value: employee.id,
                    text: employee.last_name + ", " + employee.first_name,
                };
            });
        },
        user_group() {
            return this.$page.props.auth.user.employee.user_group.name;
        },
    },
    methods: {
        updatedPerPage(value) {
            this.per_page = value;
        },
        getSelectedLead(data) {
            this.selected_lead = data;
        },
        submitAssigned() {
            this.form.employee_id = this.selected_employee;
            this.form.lead_ids = this.selected_lead;

            this.$bvModal.hide("assign-modal");

            router.post("/assigned-employees", this.form);
            this.selected_employee = "";
            this.selected_lead = [];
        },
        submitAssignedExhibitor() {
            this.form.employee_id = this.selected_exhibitor;
            this.form.lead_ids = this.selected_lead;

            this.$bvModal.hide("assign-exhibitor-modal");

            router.post("/assign-exhibitor", this.form);
            this.selected_exhibitor = "";
            this.selected_lead = [];
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
