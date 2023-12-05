<template>
    <div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Module</th>
                    <th class="text-center">Read</th>
                    <th class="text-center">Create</th>
                    <th class="text-center">Update</th>
                    <th class="text-center">Delete</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(module, index) in module_list" :key="index">
                    <td>{{ module }}</td>
                    <td class="text-center">
                        <b-form-checkbox
                            :id="'read-' + module"
                            :name="'read-' + module"
                            :value="getPermissionId(module, 'read')"
                            size="lg"
                            v-model="selected"
                            :disabled="disabled"
                            @change="clickSelected"
                        >
                        </b-form-checkbox>
                    </td>
                    <td class="text-center">
                        <b-form-checkbox
                            :id="'create-' + module"
                            :name="'create-' + module"
                            :value="getPermissionId(module, 'create')"
                            size="lg"
                            v-model="selected"
                            :disabled="disabled"
                            @change="clickSelected"
                        >
                        </b-form-checkbox>
                    </td>
                    <td class="text-center">
                        <b-form-checkbox
                            :id="'update-' + module"
                            :name="'update-' + module"
                            :value="getPermissionId(module, 'update')"
                            size="lg"
                            v-model="selected"
                            :disabled="disabled"
                            @change="clickSelected"
                        >
                        </b-form-checkbox>
                    </td>
                    <td class="text-center">
                        <b-form-checkbox
                            :id="'delete-' + module"
                            :name="'delete-' + module"
                            :value="getPermissionId(module, 'delete')"
                            size="lg"
                            v-model="selected"
                            :disabled="disabled"
                            @change="clickSelected"
                        >
                        </b-form-checkbox>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: {
        permissions: Array,
        modules: Array,
        user_group_permissions: Array,
        disabled: Boolean,
    },
    data() {
        return {
            module_list: this.modules.map((item) => {
                return item.module;
            }),
            selected: [...this.getSelectedPermissions()],
        };
    },
    methods: {
        // get the id's of permission by module & type
        getPermissionId(module, type) {
            const permission = this.permissions.find(
                (item) => item.module == module && item.type == type
            );

            if (permission) {
                return permission.id;
            }

            return 0;
        },
        // get the permissions assigned to user groups
        getSelectedPermissions() {
            // check if user group permission exists
            if (this.user_group_permissions) {
                return this.user_group_permissions.map((item) => {
                    return item.permission_id;
                });
            }

            return [];
        },
        clickSelected() {
            this.$emit("select-permission", this.selected);
        },
    },
    // run methods after mounting the component
    beforeMount() {
        this.clickSelected();
    },
};
</script>
