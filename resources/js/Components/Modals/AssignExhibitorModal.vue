<template>
    <b-modal title="Assign to Exhibitor" id="assign-exhibitor-modal">
        <b-form>
            <b-form-select
                v-model="selectedExhibitorId"
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
                @click="$bvModal.hide('assign-exhibitor-modal')"
                >Close</b-button
            >
            <b-button
                variant="success"
                type="button"
                @click="submit"
                v-if="selectedExhibitorId != ''"
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
        exhibitors: Array,
        exhibitor: Number,
    },
    data() {
        return {
            selectedExhibitorId: this.exhibitor,
        };
    },
    computed: {
        exhibitorList() {
            if (this.exhibitors.length > -1) {
                return this.exhibitors.map((employee) => {
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
            return this.$emit("submit-assigned-exhibitor",this.selectedExhibitorId);
        },
    },
};
</script>
