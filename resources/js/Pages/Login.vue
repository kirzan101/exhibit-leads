<template>
    <div>
        <head>
            <title>LeadGen | Login</title>
        </head>
        <div class="sidenav">
            <div class="login-main-text">
                <h1 class="font-weight-bold">LeadGen</h1>
                <p>
                    Lead Generation System
                    <br />
                    <small>v{{ this.$page.props.app.version }}</small>
                </p>
                <!-- <br /> -->
            </div>
        </div>
        <div class="main">
            <div class="col-md-6 col-sm-12">
                <div class="login-form">
                    <b-form @submit.prevent="login">
                        <b-form-group
                            id="email"
                            label="Email"
                            label-for="email-input"
                        >
                            <b-form-input
                                type="email"
                                placeholder="Enter Email"
                                required
                                v-model="form.email"
                                :state="errors.email ? false : null"
                                id="email-input"
                            ></b-form-input>
                            <b-form-invalid-feedback
                                :state="errors.email ? false : null"
                            >
                                {{ errors.email }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-form-group
                            id="password"
                            label="Password"
                            label-for="password-input"
                        >
                            <b-form-input
                                type="password"
                                placeholder="Enter Password"
                                required
                                v-model="form.password"
                                id="password-input"
                            ></b-form-input>
                        </b-form-group>

                        <b-button type="submit" variant="info">Login</b-button>
                    </b-form>
                </div>
            </div>
            <!-- Footer -->
            <footer>
                <!-- Copyright -->
                <div class="text-center mt-5 py-3">
                    © {{ getCurrentYear }} LeadGen v{{ this.$page.props.app.version }}
                    <br />
                    Astoria ICT
                </div>
                <!-- Copyright -->
            </footer>
            <!-- Footer -->
        </div>
    </div>
</template>

<script>
import { router } from "@inertiajs/vue2";

export default {
    layout: null,
    props: {
        errors: Object,
    },
    data() {
        return {
            form: {
                email: null,
                password: null,
            },
        };
    },
    computed: {
        getCurrentYear() {
            const date = new Date();
            return date.getFullYear();
        },
    },
    methods: {
        login() {
            router.post("/login", this.form);
        },
    },
};
</script>
