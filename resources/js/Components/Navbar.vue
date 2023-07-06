<template>
    <div>
        <b-navbar toggleable="lg" type="dark" variant="info">
            <!-- <b-navbar-brand href="/">NavBar</b-navbar-brand> -->
            <Link href="/" target="_self" class="navbar-brand">Exhibit-Leads</Link>

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    <li class="nav-item" v-if="check_access('members', 'read')">
                        <Link class="nav-link" href="/members">Members</Link>
                    </li>
                    <li class="nav-item" v-if="check_access('assigns', 'read')">
                        <Link class="nav-link" href="/assigned-employees"
                            >Assign</Link
                        >
                    </li>
                    <li class="nav-item" v-if="check_access('employees', 'read')">
                        <Link class="nav-link" href="/employees"
                            >Employees</Link
                        >
                    </li>
                    <!-- <b-nav-item href="#" disabled>Disabled</b-nav-item> -->
                </b-navbar-nav>

                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto">
                    <!-- <b-nav-form>
                        <b-form-input
                            size="sm"
                            class="mr-sm-2"
                            placeholder="Search"
                        ></b-form-input>
                        <b-button size="sm" class="my-2 my-sm-0" type="submit"
                            >Search</b-button
                        >
                    </b-nav-form> -->

                    <!-- <b-nav-item-dropdown text="Lang" right>
                        <b-dropdown-item href="#">EN</b-dropdown-item>
                        <b-dropdown-item href="#">ES</b-dropdown-item>
                        <b-dropdown-item href="#">RU</b-dropdown-item>
                        <b-dropdown-item href="#">FA</b-dropdown-item>
                    </b-nav-item-dropdown> -->

                    <b-nav-item-dropdown right>
                        <!-- Using 'button-content' slot -->
                        <template #button-content>
                            <em>{{ username }}</em>
                        </template>
                        <!-- <b-dropdown-item href="#">Profile</b-dropdown-item> -->
                        <li role="presentation">
                            <Link role="menuitem" href="/logout" method="post" class="dropdown-item" as="button">Logout</Link>
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
    }
};
</script>
