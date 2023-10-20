<template>
    <b-modal
        ref="confirmer-venue-modal"
        centered
        :title="title"
        no-close-on-esc
        no-close-on-backdrop
        hide-header-close
    >
        <div class="d-block">
            <h5>Please choose venues:</h5>
            <b-form-group
                label=""
                v-slot="{ ariaDescribedby }"
            >
                <!-- <b-form-checkbox-group
                    id="checkbox-group-1"
                    :options="
                        venue_list.map((item) => {
                            return {
                                text: item.name,
                                value: item.id,
                            };
                        })
                    "
                    :aria-describedby="ariaDescribedby"
                    name="veunue-list"
                    v-model="current_venues"
                ></b-form-checkbox-group> -->

                <b-form-radio-group
                    id="radio-slots"
                    v-model="current_venues"
                    :options="
                        venue_list.map((item) => {
                            return {
                                text: item.name,
                                value: item.id,
                            };
                        })
                    "
                    :aria-describedby="ariaDescribedby"
                    name="radio-options-slots"
                ></b-form-radio-group>
            </b-form-group>
        </div>

        <template #modal-footer>
            <!-- <b-button
                v-if="current_venues.length > 0"
                variant="info"
                @click="submit()"
            >
                Confirm
            </b-button> -->
            <b-button
                v-if="current_venues > 0"
                variant="info"
                @click="submit()"
            >
                Confirm
            </b-button>
            <b-button v-else variant="info" :disabled="true">
                Confirm
            </b-button>
        </template>
    </b-modal>
</template>

<script>
import { router } from "@inertiajs/vue2";
export default {
    props: {
        title: String,
        venue_list: Array,
        employee_venue_list: Array,
        employee_id: Number,
    },
    data() {
        return {
            // current_venues: [],
            current_venues: "",
        };
    },
    methods: {
        submit() {
            // const form = {
            //     venue_ids: this.current_venues,
            //     employee_id: this.employee_id,
            // };

            //for radio button
            // remove if requested is multiple
            const venue_id = [];
            venue_id.push(this.current_venues); // convert to array

            const form = {
                // venue_ids: this.current_venues, //for checkbox
                venue_ids: venue_id,
                employee_id: this.employee_id,
            };

            router.post("/employee-venues", {
                _method: "PUT",
                ...form,
            });
        },
        showConfirmerVenueModal() {
            this.$refs["confirmer-venue-modal"].show();
        },
    },
    // watch: {
    //     current_venues() {
    //         if (this.employee_venue_list.length > 0) {
    //             return (this.current_venues = employee_venue_list.map(
    //                 (item) => {
    //                     return item.venue_id;
    //                 }
    //             ));
    //         }

    //         return [];
    //     },
    // },
    mounted() {
        this.showConfirmerVenueModal();
    },
};
</script>
