<template>
    <div>
        <b-navbar toggleable="lg" type="dark" variant="info">
            <!-- <b-navbar-brand href="/">NavBar</b-navbar-brand> -->
            <Link href="/" target="_self" class="navbar-brand"
                >Exhibit-Leads</Link
            >

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    <li class="nav-item" v-if="check_access('leads', 'read')">
                        <Link class="nav-link" href="/leads">Leads</Link>
                    </li>
                    <li class="nav-item" v-if="check_access('assigns', 'read')">
                        <Link class="nav-link" href="/assigned-employees"
                            >Assign</Link
                        >
                    </li>
                    <li class="nav-item" v-if="check_access('invites', 'read')">
                        <Link class="nav-link" href="/invites"
                            >For Confirmation</Link
                        >
                    </li>
                    <li
                        class="nav-item"
                        v-if="check_access('confirms', 'read')"
                    >
                        <Link class="nav-link" href="/assigned-confirmers"
                            >Confirm</Link
                        >
                    </li>
                    <!-- <b-nav-item href="#" disabled>Disabled</b-nav-item> -->

                    <b-nav-item-dropdown ref="managedd" text="Manage" right>
                        <li role="presentation">
                            <Link
                                role="menuitem"
                                href="/employees"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                >Employees</Link
                            >
                            <Link
                                role="menuitem"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                href="/venues"
                                >Venues</Link
                            >
                            <Link
                                role="menuitem"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                href="/sources"
                                >Sources</Link
                            >
                        </li>
                    </b-nav-item-dropdown>
                </b-navbar-nav>

                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto">
                    <b-nav-item-dropdown ref="dropdown" right>
                        <!-- Using 'button-content' slot -->
                        <template #button-content>
                            <em>{{ fullName }}</em>
                        </template>
                        <!-- <b-dropdown-item href="#">Profile</b-dropdown-item> -->
                        <li role="presentation">
                            <Link
                                role="menuitem"
                                href="/profile"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                >Profile</Link
                            >
                        </li>
                        <li role="presentation">
                            <Link
                                role="menuitem"
                                href="/logout"
                                target="_self"
                                method="post"
                                class="dropdown-item"
                                as="button"
                                >Logout</Link
                            >
                        </li>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue2";
export default {
    components: {
        Link,
    },
    computed: {
        username() {
            return this.$page.props.auth.user.username;
        },
        fullName() {
            return this.$page.props.auth.user.full_name;
        },
    },
    methods: {
        check_access(module, type) {
            let permissions = this.$page.props.auth.permissions;

            let access = permissions
                .filter((item) => item.module === module)
                .map((element) => ({
                    module: element.module,
                    type: element.type,
                }));

            return access.some((item) => item.type === type);
        },
        closeDropDown() {
            this.$refs.dropdown.hide(true); // dropdown for user profile
            this.$refs.managedd.hide(true); // drop down for manage
            // console.log("clicked dropdown");
        },
    },
};
</script>
