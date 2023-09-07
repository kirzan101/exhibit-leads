<template>
    <div>
        <Head>
            <title>Home</title>
        </Head>
        <div class="container">
            <h3>Welcome {{ fullName }}!</h3>

            <br />

            <div>
                <b-card-group deck>
                    <b-card
                        title="Leads"
                    >
                        <b-card-text
                            >Add lead information</b-card-text
                        >
                        <Link href="/leads" class="btn btn-info" v-if="check_access('leads', 'read')">Go to leads</Link>
                        <b-btn variant="success" disabled v-else>Go to leads</b-btn>
                    </b-card>

                    <b-card
                        title="Assigned lead"
                    >
                        <b-card-text
                            >Assign lead to an employee</b-card-text
                        >
                        <Link href="/assigned-employees" class="btn btn-info" v-if="check_access('assigns', 'read')">Go to assign</Link>
                        <b-btn variant="info" disabled v-else>Go to assign</b-btn>
                    </b-card>

                    <b-card
                        title="Confirm lead"
                    >
                        <b-card-text
                            >Mark the leads as confirmed</b-card-text
                        >
                        <Link href="/confirms" class="btn btn-info" v-if="check_access('confirms', 'read')">Go to confirm</Link>
                        <b-btn variant="info" disabled v-else>Go to confirm</b-btn>
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
        fullName() {
            return this.$page.props.auth.user.full_name;
        }
    },
    methods: {
        check_access(module, type) {
            let permissions = this.$page.props.auth.permissions;

            let access = permissions.filter(item => item.module === module).map(element => ({
                module: element.module,
                type: element.type
            }))

            return access.some(item => item.type === type);
        },
    }
};
</script>
