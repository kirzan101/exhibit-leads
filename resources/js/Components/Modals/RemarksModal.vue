<template>
    <b-modal id="remarks-modal" :title="title">
        <b-form-textarea
            id="textarea"
            v-model="form.remarks"
            placeholder="Enter something..."
            rows="3"
            max-rows="6"
        ></b-form-textarea>
        <p class="mt-2 mb-2">Lead status:</p>
        <b-form-select
            v-model="form.lead_status"
            :options="lead_status_options"
            :disabled="isShow"
        ></b-form-select>
        <p class="mt-2 mb-2">Venue:</p>
        <b-form-select
            v-model="form.venue_id"
            :options="venue_options"
            :disabled="isShow"
        ></b-form-select>
        <p class="mt-2 mb-2">Presentation:</p>
        <b-input-group>
            <b-form-input
                type="date"
                id="presentation-date"
                v-model="form.presentation_date"
                :disabled="isShow"
            ></b-form-input>
            <b-form-input
                type="time"
                id="presentation-time"
                v-if="form.presentation_date"
                v-model="form.presentation_time"
                :disabled="isShow"
            ></b-form-input>
            <b-form-input
                type="time"
                id="presentation-time"
                disabled
                v-else
            ></b-form-input>
        </b-input-group>
        <p class="mt-3" v-if="updated_by !== ''">
            Last remark by: <b>{{ updated_by }}</b>
        </p>
        <template #modal-footer>
            <b-button
                variant="danger"
                type="button"
                @click="$bvModal.hide('remarks-modal')"
                >Close</b-button
            >
            <b-button
                variant="success"
                type="button"
                @click="submit"
                v-if="
                    form.remarks != null &&
                    form.remarks.length > 0 &&
                    form.lead_status != null &&
                    !isShow
                "
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
        updated_by: String,
        title: String,
        venues: Array,
        status_list: Array,
        form: Object,
        isShow: Boolean,
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
            venue_options: [
                { value: null, text: "-- select --" },
                ...this.venues.map((item) => {
                    return {
                        value: item.id,
                        text: item.name,
                    };
                }),
            ],
        };
    },
    methods: {
        submit() {
            return this.$emit("submit-remarks", this.form);
        },
    },
};
</script>
