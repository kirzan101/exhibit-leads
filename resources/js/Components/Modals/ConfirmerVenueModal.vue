<template>
    <b-modal
        ref="my-modal"
        hide-footer
        title="Using Component Methods"
        no-close-on-esc
        no-close-on-backdrop
        hide-header-close
    >
        <div class="d-block text-center">
            <h3>Hello From My Modal!</h3>
        </div>
        <b-button class="mt-3" variant="outline-danger"
            >Close Me</b-button
        >
        <b-button
            class="mt-2"
            variant="outline-warning"
            block
            >Toggle Me</b-button
        >
    </b-modal>
</template>
