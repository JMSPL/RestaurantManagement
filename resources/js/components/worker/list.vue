<template>
    <div>
        <button class="btn right" @click="create">Create worker</button>
        <edit-create :worker="currentWorker" v-if="operation !== ''" :operation="operation" @save="save" @cancel="cancel"></edit-create>
        <vuetable ref="vuetable"
                  api-url="/api/workers"
                  :fields="fields"
                  pagination-path=""
                  :css="css.table"
                  :http-options="httpOptions"
                  @vuetable:pagination-data="onPaginationData"
        >
            <div slot="blocked" slot-scope="props">
                <i @click="unblock(props.rowData)" v-if="props.rowData.blocked" class="material-icons tooltipped" data-position="right" data-tooltip="Blocked">block</i>
                <i @click="block(props.rowData)" v-else class="material-icons tooltipped" data-position="right" data-tooltip="Not blocked">check</i>
            </div>
            <div slot="actions" slot-scope="props">
                <a class="waves-effect waves-light btn blue" @click="editWorker(props.rowData)">Edit</a>
                <a class="waves-effect waves-light btn" v-if="props.rowData.id !== loggedUser.id" @click="deleteWorker(props.rowData)">Delete</a>
            </div>
            <div slot="intern" slot-scope="props">
                <p @click="change(props.rowData)"> {{props.rowData.intern==1? "Estagiario" : "Efectivo"}}</p>
            </div>
        </vuetable>
        <vuetable-pagination class="center" ref="pagination"
                             :css="css.pagination"
                             @vuetable-pagination:change-page="onChangePage"
        ></vuetable-pagination>
    </div>
</template>

<script>
    import Vuetable from 'vuetable-2';
    import VuetablePagination from '../components/VuetablePagination';
    import EditCreate from './edit-create';

    const myScripts = require('../../../../public/js/scripts');

    export default {
        components:{Vuetable,VuetablePagination,EditCreate},
        data(){
            return{
                currentWorker: [],
                operation: "",
                httpOptions:{
                    headers:{
                        Authorization: axios.defaults.headers.common.Authorization
                    }
                },
                fields: [
                    {
                        name: 'name',
                        sortField: 'name'
                    },
                    {
                        name: 'username',
                        sortField: 'username'
                    },
                    {
                        name: 'email',
                        sortField: 'email'
                    },
                    {
                        name: 'type',
                        sortField: 'type'
                    },
                    {
                        name: '__slot:blocked',
                        title: 'Blocked',
                    },
                    {
                        name: "__slot:actions",
                        title: "Actions"
                    },
                    {
                        name: '__slot:intern',
                        title: 'Intern'
                    }
                ],
                css:{
                    table:{
                        tableWrapper: "",
                        tableHeaderClass: "mb-0",
                        tableBodyClass: "mb-0",
                        tableClass: "table table-bordered table-hover",
                        loadingClass: "loading",
                        ascendingIcon: "arrow_upward",
                        descendingIcon: "arrow_downward",
                        ascendingClass: "sorted-asc",
                        descendingClass: "sorted-desc",
                        sortableIcon: "swap_vert",
                        detailRowClass: "vuetable-detail-row",
                        handleIcon: "fa-bars text-secondary",
                        renderIcon(classes, options) {
                            return `<i class="material-icons" ${options}>${classes[1]}</span>`;
                        },
                    },
                    pagination: {
                        wrapperClass: 'pagination',
                        activeClass: 'active',
                        disabledClass: 'disabled',
                        pageClass: 'page',
                        linkClass: 'link',
                        icons: {
                            first: '',
                            prev: '',
                            next: '',
                            last: '',
                        },
                    },
                }
            }
        },
        created(){
        },
        methods:{
            onPaginationData (paginationData) {
                this.$refs.pagination.setPaginationData(paginationData);
                myScripts.startInstances();
            },
            onChangePage (page) {
                this.$refs.vuetable.changePage(page);
                myScripts.startInstances();
            },
            deleteWorker(worker){
                let request = axios.delete('/api/workers/'+worker.id);
                request.then(res => {
                    this.$refs.vuetable.reload();
                    M.toast({html: '\"'+worker.name+'\" deleted!'})
                });
                request.catch(res => {
                    M.toast({html: 'Something went wrong!', classes:"red"})
                })
            },
            editWorker(worker){
                this.currentWorker = worker;
                this.operation = "Edit";
            },
            create(){
                this.currentWorker = {};
                this.operation = "Create";
            },
            save(worker){
                if(this.operation === "Edit"){
                    Object.assign(this.currentWorker, worker);
                }else{
                    this.$refs.vuetable.reload();
                }
                this.currentWorker = null;
                this.operation = "";
            },
            cancel(worker){
                this.operation = "";
                Object.assign(this.currentWorker, worker);
                this.currentWorker = null;
            },
            block(worker){
                let request = axios.post('/api/workers/'+worker.id+'/block');
                request.then(res => {
                    console.log(res.data.name);
                    M.toast({html:'\"'+res.data.name+'\" blocked!'});
                    Object.assign(worker, res.data);
                });
                request.catch(res => M.toast({html:res.response.data.message, classes: "red"}));
            },
            unblock(worker){
                let request = axios.post('/api/workers/'+worker.id+'/unblock');
                request.then(res => {
                    M.toast({html:'\"'+res.data.name+'\" unblocked!'});
                    Object.assign(worker, res.data);
                });
                request.catch(res => M.toast({html:res.response.data.message, classes: "red"}));
            },
            change(worker){
                let request = axios.post('/api/workers/'+worker.id+'/changestatus');
                request.then(res => {
                    M.toast({html:'\"'+res.data.name+'\" status changed!'});
                    Object.assign(worker, res.data);
                });
                request.catch(res => M.toast({html:res.response.data.message, classes: "red"}));
            }
        },
        mounted(){
            myScripts.startInstances();
        },
        computed: {
            loggedUser(){
                return this.$store.state.user;
            }
        }
    }
</script>

<style scoped>

</style>
