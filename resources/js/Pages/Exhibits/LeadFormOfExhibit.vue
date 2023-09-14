<template>
    <b-container fluid>
        <Head>
            <title v-if="is_disabled">Exhibit Lead | Show</title>
            <title v-else>Exhibit Lead | Edit</title>
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

        <h5>
            <div v-if="is_disabled">
                Show Lead |
                <Link class="btn btn-secondary" :href="'/' + form_type"
                    >Back</Link
                >
                <Link
                    class="btn btn-success float-right"
                    v-if="is_disabled"
                    :href="'/' + form_type + '/edit/' + lead.id"
                    >Edit Lead</Link
                >
            </div>
            <div v-else>
                Edit Lead |
                <Link class="btn btn-secondary" :href="'/' + form_type + '/' + lead.id"
                    >Back</Link
                >
            </div>
        </h5>
        <span class="note">Note: Fields that has "*" is required</span>
        <LeadForm
            :is_disabled="is_disabled"
            :form_type="form_type"
            :errors="errors"
            :lead="lead"
            :properties="properties"
            :venues="venues"
            :sources="sources"
        />
    </b-container>
</template>

<script>
import { Link } from "@inertiajs/vue2";
import LeadForm from "../../Components/LeadForm.vue";

export default {
    components: {
        Link,
        LeadForm,
    },
    props: {
        is_disabled: Boolean,
        form_type: String,
        errors: Object,
        lead: Object,
        properties: Array,
        venues: Array,
        sources: Array,
    },
    methods: {
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
