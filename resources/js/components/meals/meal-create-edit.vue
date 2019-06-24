<template>
    <div class="row">
        <div class="col l12 m12 s12">
            <h3 class="header">{{ operation }} Meal</h3>
            <div class="card horizontal">
                <div class="card-stacked">
                    <div class="card-content">
                        <div class="row">
                            <div class="input-field col s12 m6 l6">
                                <select id="select"name="table_number" v-model="table">
                                    <option value="" selected> -- Select a Table -- </option>
                                    <option v-for="table in tables" :value="table.table_number" :class="{selected: table.table_number === this.table}">{{ table.table_number }}</option>
                                </select>
                                <label>Table</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-action right-align">
                        <button class="btn waves-effect waves-light grey" @click="cancel">Cancel</button>
                        <button class="btn waves-effect waves-light" @click="save">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const myScripts = require('../../../../public/js/scripts')
    export default {
        props:['meal','operation'],
        data: function(){
            return {
                file: '',
                errors: [],
                tables: [],
                table: "",
            }
        },
        mounted: function () {
            myScripts.startInstances();
            M.updateTextFields();
        },
        methods:{
            save(){
                let formData = new FormData();
                formData.append('file', this.file);

                this.meal.table_number = this.table;
                this.meal.responsible_waiter_id = this.$store.state.user.id;

                axios.post('/api/meals', this.meal)
                    .then(res=>{
                        M.toast({html: '\"'+res.data.id+'\" created!'});
                        this.$emit('save');
                    })
                    .catch(res => {
                        this.errors = res.errors;
                    });
            },
            cancel(){
                let request = axios.get('/api/meals/'+this.meal.id);
                request.then(res => this.$emit('cancel', res.data.data));
            },
            handleFileUpload: function(){
                this.file = this.$refs.file.files[0];
            }
        },
        created(){
            let request = axios.get('/api/tables/available');
            request.then(res => this.tables = res.data.data);
        }
    }
</script>
