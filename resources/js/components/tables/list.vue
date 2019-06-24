<template>
    <div>
        <div class="row">
            <div class="col s12 m12 l12">
                <button class="btn waves-effect right modal-trigger" href="#edit-create-modal" @click="operation = 'Create'">Add table</button>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m6 l4" v-for="table in tables">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Table #{{ table.table_number }}</span>
                    </div>
                    <div class="card-action">
                        <a @click="editTable(table)" href="#edit-create-modal" class="modal-trigger">Edit</a>
                        <a @click="deleteTable(table)">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="edit-create-modal" class="modal modal-fixed-footer bottom-sheet">
            <div class="modal-content">
                <h4>{{ title }}</h4>
                <div class="input-field col s6 offset-s3">
                    <input id="table_number" placeholder="Table Number"  type="number" min="0" step="0.01" v-model="currentTable.table_number">
                    <label for="table_number" class="active">Table Number</label>
                </div>
            </div>
            <div class="modal-footer">
                <a @click="cancel" class="modal-close waves-effect waves-red btn-flat">Cancel</a>
                <a @click="save" class="modal-close waves-effect waves-green btn-flat">Agree</a>
            </div>
        </div>
    </div>
</template>

<script>

    const myScripts = require('../../../../public/js/scripts');

    export default {
        name: "list",
        data(){
            return {
                tables: [],
                currentTable: [],
                originalTable: [],
                operation: "",
            }
        },
        methods: {
            deleteTable(table){
                let request = axios.delete('/api/tables/'+table.table_number);
                request.then(res => {
                    this.tables.splice(this.tables.indexOf(table), 1);
                    M.toast({html: "Successful deleted!"});
                })
            },
            editTable(table){
                this.operation = "Edit";
                this.currentTable = table;
                this.originalTable = Object.assign({},table);
            },
            cancel(){
                let request = axios.get('/api/tables/'+this.originalTable.table_number);
                request.then(res => {
                    Object.assign(this.currentTable, res.data.data)
                });
            },
            save(){
                if(this.operation === "Create"){
                    let request = axios.post('/api/tables', this.currentTable);
                    request.then(res => {
                        this.tables.push(this.currentTable);
                        M.toast({html: "Table created successfully!"})
                    });

                    request.catch(res => {
                        this.cancel();
                        M.toast({html: res.response.data, classes: "red"})
                    })
                }else if(this.operation === "Edit"){
                    let request = axios.put('/api/tables/'+this.originalTable.table_number, this.currentTable);
                    request.then(res => {
                        Object.assign(this.originalTable, this.currentTable);
                        M.toast({html: "Table updated successfully!"})
                    });

                    request.catch(res => {
                        this.cancel();
                        M.toast({html: res.response.data, classes: "red"})
                    })
                }
            },
        },
        mounted(){
            myScripts.startInstances();
        },
        watch:{
            operation(val){
                if(val === "Create"){
                    this.currentTable = {};
                }
            }
        },
        created(){
            axios.get('/api/tables')
                .then(res => this.tables = res.data.data);
        },
        computed:{
            title(){
                if(this.operation === "Edit") {
                    return this.operation+" Table #" + this.originalTable.table_number;
                }
                return this.operation;
            }
        },
    }
</script>

<style scoped>

</style>