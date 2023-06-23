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
                Member |
                <Link href="/members/create" class="btn btn-success">Add</Link>
            </h5>

            <br />

            <div>
                <b-table
                    striped
                    hover
                    :fields="fields"
                    :items="members"
                ></b-table>
            </div>
        </b-container>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue2";

export default {
    components: {
        Link,
    },
    props: {
        members: Array,
    },
    data() {
        return {
            showDismissibleAlert: false,
            fields: ["first_name", "last_name", "address"],
        };
    },
};
</script>
