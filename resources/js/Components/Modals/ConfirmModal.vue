<template>
    <b-modal id="confirm-modal" title="Confirm Lead:">
        <p class="mt-2 mb-2">Remarks:</p>
        <b-form-textarea
            id="textarea"
            placeholder="Enter something..."
            rows="3"
            max-rows="6"
            v-model="form.remarks"
            :disabled="isShow"
        ></b-form-textarea>
        <p class="mt-2 mb-2">Lead status:</p>
        <b-form-select
            v-model="form.lead_status"
            :options="lead_status_options"
            :disabled="isShow"
        ></b-form-select>

        <template #modal-footer>
            <b-button
                variant="danger"
                type="button"
                @click="$bvModal.hide('confirm-modal')"
                >Close</b-button
            >
            <b-button
                variant="success"
                type="button"
                @click="submit"
                v-if="
                    (form.remarks != null && form.remarks.length > 0) &&
                    (form.lead_status != null && form.lead_status.length > 0) &&
                    !isShow
                "
                >Save</b-button
            >
            <b-button v-else variant="success" type="button" disabled
                >Save</b-button
            >
        </template>
    </b-modal>
</template>

<script>
export default {
    props: {
        title: String,
        status_list: Array,
        form: Object,
        isShow: Boolean
    },
    data() {
        return {
            lead_status_options: [
                { value: null, text: "-- select --" },
                ...this.status_list.map((item) => {
                    return {
                        value: item.name,
                        text: item.name + " " + "(" + item.code + ")",
                    };
                }),
            ],
        };
    },
    methods: {
        submit() {
            return this.$emit("submit-confirm", this.form);
        },
    },
};
</script>
