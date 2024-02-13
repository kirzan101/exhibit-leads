<template>
    <b-container fluid>
        <Head>
            <title v-if="is_disabled">Scanned Lead | Show</title>
            <title v-else>Scanned Lead | Edit</title>
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
            <div>
                Create Lead |
                <Link class="btn btn-secondary" :href="'/scan'"
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
