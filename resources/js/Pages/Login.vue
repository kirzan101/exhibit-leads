<template>
    <div class="container lg:p-10">
        <head>
            <title>LeadGen | Login</title>
        </head>
        <div class="col-lg-4 col-md-6 mx-auto">
            <b-card class="mt-5">
                <div class="mx-auto">
                    <h1 class="text-center">LeadGen</h1>
                    <h3 class="text-center">Login</h3>
                    <b-form @submit.prevent="login">
                        <b-form-group
                            id="email"
                            label="Email"
                            label-for="email"
                        >
                            <b-form-input
                                type="email"
                                placeholder="Enter Email"
                                required
                                v-model="form.email"
                                :state="errors.email ? false : null"
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
                            label-for="password"
                        >
                            <b-form-input
                                type="password"
                                placeholder="Enter Password"
                                required
                                v-model="form.password"
                            ></b-form-input>
                        </b-form-group>

                        <b-button type="submit" variant="success">Login</b-button>
                    </b-form>
                </div>
            </b-card>
        </div>
    </div>
</template>

<script>
import { router } from "@inertiajs/vue2";

export default {
    layout: null,
    props: {
        errors: Object
    },
    data() {
        return {
            form: {
                email: null,
                password: null,
            },
        };
    },
    methods: {
        login() {
            router.post("/login", this.form);
        },
    },
};
</script>
