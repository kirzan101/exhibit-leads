<template>
    <div>
        <head>
           <title> Selected | test</title>
        </head>
      <b-form-group>
        <template #label>
          <b>Choose your flavours:</b><br>
          <b-form-checkbox
            v-model="allSelected"
            :indeterminate="indeterminate"
            aria-describedby="flavours"
            aria-controls="flavours"
            @change="toggleAll"
          >
            {{ allSelected ? 'Un-select All' : 'Select All' }}
          </b-form-checkbox>
        </template>
  
        <template v-slot="{ ariaDescribedby }">
          <b-form-checkbox-group
            id="flavors"
            v-model="selected"
            :options="flavours"
            :aria-describedby="ariaDescribedby"
            name="flavors"
            class="ml-4"
            aria-label="Individual flavours"
          ></b-form-checkbox-group>
        </template>
      </b-form-group>

      <!-- selected two -->
      <b-form-group>
        <template #label>
          <b>Choose your flavours:</b><br>
          <b-form-checkbox
            v-model="allSelected"
            aria-describedby="flavours2"
            aria-controls="flavours2"
            @change="toggleAll"
          >
            {{ allSelected ? 'Un-select All' : 'Select All' }}
          </b-form-checkbox>
        </template>
  
        <template v-slot="{ ariaDescribedbyTwo }">
          <b-form-checkbox-group
            id="flavors2"
            v-model="selected_two"
            :options="flavours"
            :aria-describedby="ariaDescribedbyTwo"
            name="flavors2"
            class="ml-4"
            aria-label="Individual flavours"
          ></b-form-checkbox-group>
        </template>
      </b-form-group>
  
      <div>
        Selected: <strong>{{ selected }}</strong><br>
        Selected two: <strong>{{ selected_two }}</strong><br>
        All Selected: <strong>{{ allSelected }}</strong><br>
        Indeterminate: <strong>{{ indeterminate }}</strong>
      </div>
    </div>
  </template>
  
  <script>
    export default {
      data() {
        return {
          flavours: ['Orange', 'Grape', 'Apple', 'Lime', 'Very Berry'],
          selected: [],
          selected_two: [],
          allSelected: false,
          indeterminate: false
        }
      },
      methods: {
        toggleAll(checked) {
          console.log(checked, event);
          this.selected = checked ? this.flavours.slice() : []
        }
      },
      watch: {
        selected(newValue, oldValue) {
          // Handle changes in individual flavour checkboxes
          if (newValue.length === 0) {
            this.indeterminate = false
            this.allSelected = false
          } else if (newValue.length === this.flavours.length) {
            this.indeterminate = false
            this.allSelected = true
          } else {
            this.indeterminate = true
            this.allSelected = false
          }
        }
      }
    }
  </script>