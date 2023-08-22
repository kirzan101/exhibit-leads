<template>
    <b-modal title="Assign to Employee" id="assign-modal">
        <b-form>
            <b-form-select
                v-model="selectedEmployeeId"
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
                @click="submit"
                v-if="selectedEmployeeId != ''"
                >Submit</b-button
            >
            <b-button variant="success" type="button" disabled v-else
                >Submit</b-button
            >
        </template>
    </b-modal>
</template>

<script>
export default {
    props: {
        employees: Array,
    },
    data() {
        return {
            selectedEmployeeId: "",
        };
    },
    computed: {
        employeeList() {
            if (this.employees.length > -1) {
                return this.employees.map((employee) => {
                    return {
                        value: employee.id,
                        text: employee.last_name + ", " + employee.first_name,
                    };
                });
            }

            return {
                value: null,
                text: "-- select --",
            };
        },
    },
    methods: {
        submit() {
            return this.$emit(
                "submit-assigned-employee",
                this.selectedEmployeeId
            );
        },
    },
};
</script>
