<template>
    <div class="mailchimp-merge-fields-fieldtype-wrapper">
        <small class="help-block text-grey-60" v-if="!list">{{ __('Select list') }}</small>

        <v-select
            append-to-body
            v-if="showFieldtype && list"
            v-model="selected"
            :clearable="true"
            :options="fields"
            :reduce="(option) => option.id"
            :searchable="true"
            @input="$emit('input', $event)"
        />
    </div>
</template>

<script>
export default {

    mixins: [Fieldtype],

    inject: ['storeName'],

    data() {
        return {
            fields: [],
            selected: null,
            showFieldtype: true,
        }
    },

    watch: {
        list(list) {
            this.showFieldtype = false;

            this.refreshFields();

            this.$nextTick(() => this.showFieldtype = true);
        }
    },

    computed: {
        key() {
            let matches = this.namePrefix.match(/([a-z_]*?)\[(.*?)\]/);
            
            if (matches[1] == 'campaign_monitor') { // form page
                return 'campaign_monitor.settings.list_id.0';                
            }
            
            return matches[0].replace('[', '.').replace(']', '.') + 'list_id.0';
        },

        list() {
            return data_get(this.$store.state.publish[this.storeName].values, this.key)
        },
    },

    mounted() {
        this.selected = this.value;
        this.refreshFields();
    },

    methods: {
        refreshFields() {
            this.$axios
                .get(cp_url(`/campaign-monitor/custom-fields/${this.list}`))
                .then(response => {
                    this.fields = response.data;
                })
                .catch(() => { this.fields = []; });
        }
    }
};
</script>
