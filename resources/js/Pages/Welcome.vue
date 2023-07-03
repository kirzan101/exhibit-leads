<template>
    <div>
        <div class="container">
            <h3>Welcome {{ username }}</h3>

            <br />

            <div>
                <b-card-group deck>
                    <b-card
                        title="Member"
                    >
                        <b-card-text
                            >Add member information</b-card-text
                        >
                        <Link href="/members" class="btn btn-success">Go to members</Link>
                    </b-card>

                    <b-card
                        title="Assigned member"
                    >
                        <b-card-text
                            >Assign member to an employee</b-card-text
                        >
                        <Link href="/assigned-employees" class="btn btn-info">Go to assign</Link>
                    </b-card>
                </b-card-group>
            </div>
        </div>
    </div>
</template>

<script>
// import Layout from "./Layout.vue";
import { Link } from '@inertiajs/vue2';

export default {
    components: {
        Link
    },
    computed: {
        username() {
            return this.$page.props.auth.user.username;
        },
    },
};
</script>
