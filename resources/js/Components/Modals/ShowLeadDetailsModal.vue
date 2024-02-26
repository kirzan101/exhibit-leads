<template>
    <b-modal id="show-lead-details-modal" title="Show Lead details">
        <form @submit.prevent="submit">
            <div>
                <p>Name: <b>{{ lead.full_name }}</b></p>
                <p>Companion Name: <b>{{ lead.companion_full_name }}</b></p>
                <p>Mobile Number <b>{{ lead.mobile_number }}</b></p>
                <p>Occupation <b>{{ lead.occupation }}</b></p>
                <p>Age <b>{{ lead.age }}</b></p>
                <p>Addres <b>{{ lead.address }}</b></p>
                <p>Hotel <b>{{ lead.hotel }}</b></p>
                <p>Source <b>{{ lead.source }}</b></p>
                <p>Remarks <b>{{ lead.remarks }}</b></p>
            </div>
        </form>

        <template #modal-footer>
            <b-button
                variant="danger"
                type="button"
                @click="$bvModal.hide('show-lead-details-modal')"
                >Close</b-button
            >
        </template>
    </b-modal>
</template>

<script>
export default {
    props: {
        url: String,
        lead: Object,
    },
};
</script>
