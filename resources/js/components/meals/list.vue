<template>

    <div>
        <div class="row" v-if="getLoggedUser.type === 'waiter'">
            <button class="btn right" @click="create">Create meal</button>
            <meal-create-edit :meal="currentMeal" v-if="operation !== ''" :operation="operation" @save="save" @cancel="cancel"></meal-create-edit>
        </div>

        <div class="row" v-if="getLoggedUser.type !== 'waiter'">
            <ul class="tabs">
                <li class="tab" style="display:none;"><a :class="{active: state === ''}"></a></li>
                <li class="tab col s3"><a @click="setState('active')" :class="{active: state === 'active'}">Active</a></li>
                <li class="tab col s3"><a @click="setState('terminated')" :class="{active: state === 'terminated'}">Terminated</a></li>
                <li class="tab col s3"><a @click="setState('paid')" :class="{active: state === 'paid'}">Paid</a></li>
                <li class="tab col s3"><a @click="setState('not paid')" :class="{active: state === 'not paid'}">Not paid</a></li>
            </ul>
        </div>

        <div class="row" v-if="getLoggedUser.type !== 'waiter'">
            <div class="input-field col s12 m6 l6">
                <select id="select" v-model="waiter">
                    <option value="" selected> -- Select a Waiter -- </option>
                    <option v-for="waiter in waiters" :value="waiter.id" :class="{selected: waiter.id === this.waiter}">{{ waiter.name }}</option>
                </select>
                <label>Waiter</label>
            </div>
            <div class="input-field col s11 m5 l5">
                <input type="text" ref="date" class="datepicker" placeholder="Choose a date" @change="date = $refs.date.value">
            </div>
            <div class="col s1 m1 l1">
                <i class="material-icons tooltipped clear-filter" data-tooltip="Clear filters" data-position="right" @click="resetFilters">clear</i>
            </div>
        </div>

        <div class="row" v-if="getLoggedUser.type !== 'waiter'">
            <vuetable
                    ref="vuetable"
                    :api-url="'/api/meals?state='+state+'&waiter='+waiter+'&date='+date"
                    :fields="fields"
                    pagination-path=""
                    :css="css.table"
                    @vuetable:pagination-data="onPaginationData"
                    :http-options="httpOptions"
            >
                <div slot="actions" slot-scope="props">
                    <button class="btn btn-floating btn-small modal-trigger tooltipped" data-position="right" data-tooltip="Info" data-target="meal-info-modal" @click="info(props.rowData)"><i class="tiny material-icons">info</i></button>
                    <button class="btn btn-floating btn-small tooltipped" v-if="props.rowData.state !== 'active'" data-position="right" data-tooltip="Invoice" @click="invoice(props.rowData)"><i class="tiny material-icons">insert_drive_file</i></button>
                    <button class="btn btn-floating btn-small tooltipped" v-if="props.rowData.state !== 'active'" data-position="right" data-tooltip="Not paid" @click="not_paid(props.rowData)"><i class="tiny material-icons">money_off</i></button>
                </div>
            </vuetable>
            <vuetable-pagination class="center" ref="pagination"
                                 :css="css.pagination"
                                 @vuetable-pagination:change-page="onChangePage"
            ></vuetable-pagination>
        </div>

        <div id="meal-info-modal" class="modal modal-fixed-footer">
            <div class="modal-content">
                <h4>{{ table }}</h4>
                <meal-details :meal="currentMeal"></meal-details>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
            </div>
        </div>
    </div>
</template>

<script>
    import Vuetable from 'vuetable-2';
    import VuetablePagination from '../components/VuetablePagination';
    import MealDetails from './meal-details';
    import MealCreateEdit from "./meal-create-edit";
    const myScripts = require('../../../../public/js/scripts');

    export default {
        data: function(){
            return {
                currentMeal: [],
                state: "",
                waiter: "",
                date: "",
                waiters: [],
                operation:'',
                httpOptions:{
                    headers:{
                        Authorization: axios.defaults.headers.common.Authorization
                    }
                },
                fields:[
                    {
                        name: 'table_number',
                        sortField: 'table_number',
                        title:'Table',
                    },
                    {
                        name: 'waiter.name',
                        sortField: 'waiter.name',
                        title: 'Waiter',
                    },
                    {
                        name: 'created_at',
                        sortField: 'created_at',
                        title: 'Date',
                    },
                    {
                        name: 'total_price_preview',
                        sortField: 'total_price_preview',
                        title: 'Total Price',
                        callback: "price",
                    },
                    {
                        name:'__slot:actions',
                        title: 'Actions',
                    }
                ],
                css: {
                    table: {
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
        components:{MealCreateEdit, Vuetable,VuetablePagination,MealDetails},
        methods:{
            info(meal){
                this.currentMeal = meal;
            },
            setState(state){
                this.state = state;
            },
            onPaginationData (paginationData) {
                this.$refs.pagination.setPaginationData(paginationData);
                myScripts.startInstances();
            },
            onChangePage (page) {
                this.$refs.vuetable.changePage(page);
                myScripts.startInstances();
            },
            price(value){
                return value+" €";
            },
            not_paid(meal){
                let request = axios.post('/api/meals/'+meal.id+"/notpaid");
                request.then(res => {

                });
            },
            invoice(meal){

            },
            create(){
                this.currentMeal = {};
                this.operation = "Create";
            },
            save(meal){
                this.$refs.vuetable.reload();
                this.currentMeal = null;
                this.operation = "";
            },
            cancel(meal){
                this.operation = "";
                this.currentMeal = {};
            },
            resetFilters(){
                document.getElementsByClassName('datepicker')[0].value =
                    this.waiter = this.date = this.state = "";
            }
        },
        mounted: function(){
            myScripts.startInstances();
        },
        computed:{
            table(){
                return "Table #"+this.currentMeal.table_number;
            },
            getLoggedUser: function(){
                return this.$store.state.user;
            }
        },
        created(){
            let request = axios.get('/api/workers/waiters');
            request.then(res => this.waiters = res.data.data);
        }
    }
</script>
