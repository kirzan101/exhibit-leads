<template>
    <div>
        <b-navbar toggleable="lg" type="dark" variant="info">
            <!-- <b-navbar-brand href="/">NavBar</b-navbar-brand> -->
            <Link href="/" target="_self" class="navbar-brand"
                >LeadGen</Link
            >

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    <li class="nav-item" v-if="check_access('leads', 'read')">
                        <Link class="nav-link" href="/leads">Leads</Link>
                    </li>

                    <li class="nav-item" v-if="check_access('rois', 'read')">
                        <Link class="nav-link" href="/rois">ROIs</Link>
                    </li>

                    <li class="nav-item" v-if="check_access('surveys', 'read')">
                        <Link class="nav-link" href="/surveys">Holiday Surveys</Link>
                    </li>
                    
                    <li class="nav-item" v-if="check_access('exhibits', 'read')">
                        <Link class="nav-link" href="/exhibits">Exhibit</Link>
                    </li>

                    <li class="nav-item" v-if="check_access('status', 'read')">
                        <Link class="nav-link" href="/lead-status">Lead Status</Link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="check_access('assign-exhibitors', 'read')"
                    >
                        <Link class="nav-link" href="/assigned-exhibitors"
                            >Assigned Exhibitor</Link
                        >
                    </li>

                    <li class="nav-item" v-if="check_access('assigns', 'read')">
                        <Link class="nav-link" href="/assigned-employees"
                            >Assign</Link
                        >
                    </li>
                    <li
                        class="nav-item"
                        v-if="check_access('confirms', 'read')"
                    >
                        <Link class="nav-link" href="/confirms">Confirms</Link>
                    </li>
                    <!-- <b-nav-item href="#" disabled>Disabled</b-nav-item> -->

                    <b-nav-item-dropdown
                        ref="managemenu"
                        text="Manage"
                        v-if="check_manage_access()"
                        right
                    >
                        <li role="presentation">
                            <Link
                                v-if="check_access('employees', 'read')"
                                role="menuitem"
                                href="/employees"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                >Employees</Link
                            >
                            <Link
                                v-if="check_access('venues', 'read')"
                                role="menuitem"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                href="/venues"
                                >Venues</Link
                            >
                            <Link
                                v-if="check_access('sources', 'read')"
                                role="menuitem"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                href="/sources"
                                >Sources</Link
                            >
                            <Link
                                v-if="check_access('usergroups', 'read')"
                                role="menuitem"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                href="/usergroups"
                                >User Groups</Link
                            >
                            <Link
                                v-if="check_access('confirmed', 'read')"
                                role="menuitem"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                href="/confirmed"
                                >Confirmed</Link
                            >
                            <Link
                                v-if="check_access('activity-logs', 'read')"
                                role="menuitem"
                                target="_self"
                                class="dropdown-item"
                                @click="closeDropDown"
                                href="/activity-logs"
                                >Activity Logs</Link
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
            this.$refs.managemenu.hide(true); // drop down for manage
            // console.log("clicked dropdown");
        },
        check_manage_access() {
            //checker for manage drop down
            let result = [];
            let modules = ["employees", "venues", "sources", "confirms", "activity-logs"];
            let permissions = this.$page.props.auth.permissions;

            modules.forEach((module) => {
                if (
                    permissions.filter((item) => item.module === module)
                        .length > 0
                ) {
                    result.push(true);
                } else {
                    result.push(false);
                }
            });

            if (result.filter((item) => item === true).length > 0) {
                return true;
            } else {
                return false;
            }
        },
    },
};
</script>
