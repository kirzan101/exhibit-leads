<template>
    <div>
        <Head>
            <title>Confirms</title>
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
                    <div class="col-sm-6">Confirms</div>
                    <div class="col-sm-6">
                        <div v-if="check_access('confirms', 'create')">
                            <b-button
                                class="btn btn-danger m-1"
                                v-b-modal.remove-modal
                                style="float: right"
                                align-v="end"
                                v-if="selected_lead.length > 0"
                                >Remove</b-button
                            >
                            <b-button
                                class="btn btn-danger m-1"
                                v-b-modal.remove-modal
                                style="float: right"
                                align-v="end"
                                disabled
                                v-else
                                >Remove</b-button
                            >
                        </div>
                        <!-- remove done -->
                        <b-modal title="Notice:" id="remove-modal">
                            <p>Remove Done remarks?</p>
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
                                    @click="submitDone()"
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

            <ConfirmTable
                :fields="fields"
                :items="leads"
                :per_page="per_page"
                :occupation_list="occupation_list"
                :status_list="status_list"
                :confirmer_status_list="confirmer_status_list"
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
import ForConfirmationTable from "../../Components/ForConfirmationTable.vue"
import ConfirmTable from "../../Components/ConfirmTable.vue";

export default {
    components: {
        Link,
        ForConfirmationTable,
        ConfirmTable
    },
    props: {
        leads: Array,
        employees: Array,
        per_page: Number,
        occupation_list: Array,
        is_confirmer: Boolean,
        status_list: Array,
        confirmer_status_list: Array,
        venues: Array,
        sources: Array
    },
    data() {
        return {
            showDismissibleAlert: false,
            fields: [
                { key: "selected", label: "selected", sortable: false },
                { key: "lead_full_name", label: "Lead name", sortable: true },
                { key: "occupation", label: "Occupation", sortable: true },
                { key: "lead_status", label: "Status", sortable: true },
                { key: "venue.name", label: "Venue", sortable: true },
                { key: "source_complete", label: "Source", sortable: true },
                { key: "mobile_number", label: "Mobile Number", sortable: false },
                { key: "actions", label: "Actions" },
            ],
            selected_lead: [],
            form: {
                lead_ids: [],
            },
            assign_form: {
                lead_ids: [],
                employee_id: "",
            },
            alert: false,
            selected_employee: "",
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
        getSelectedLead(data) {
            this.selected_lead = data;
        },
        submitDone() {
            this.form.lead_ids = this.selected_lead;

            this.$bvModal.hide("remove-modal");

            router.post("/done/cancel", this.form);
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
