<template>
    <div>
        <div class="row">
            <ul class="tabs">
                <li class="tab" style="display:none;"><a :class="{active: state === ''}"></a></li>
                <li class="tab col s4"><a @click="setState('pending')" :class="{active: state === 'pending'}">Pending</a></li>
                <li class="tab col s4"><a @click="setState('paid')" :class="{active: state === 'paid'}">Paid</a></li>
                <li class="tab col s4"><a v-if="getLoggedUser.type !== 'cashier'" @click="setState('not paid')" :class="{active: state === 'not paid'}">Not paid</a></li>
            </ul>
        </div>

        <div v-if="getLoggedUser.type !== 'cashier'" class="row">
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
            <div  class="col s1 m1 l1">
                <i class="material-icons tooltipped clear-filter" data-tooltip="Clear filters" data-position="right" @click="resetFilters">clear</i>
            </div>
        </div>

        <div class="row">
            <vuetable
                    ref="vuetable"
                    :api-url="'/api/invoices?state='+state+'&waiter='+waiter+'&date='+date"
                    :fields="fields"
                    pagination-path=""
                    :css="css.table"
                    @vuetable:pagination-data="onPaginationData"
                    :http-options="httpOptions"
            >
                <div slot="actions" slot-scope="props">
                    <button v-if="getLoggedUser.type !== 'cashier' || state==='paid'" class="btn btn-floating btn-small modal-trigger tooltipped" data-position="right" data-tooltip="Items" data-target="invoice-items-modal" @click="items(props.rowData)"><i class="tiny material-icons">local_dining</i></button>
                    <button v-if="props.rowData.state === 'paid' && isAuthorized()" class="btn btn-floating btn-small tooltipped" data-position="right" data-tooltip="Download invoice" @click="download(props.rowData)"><i class="tiny material-icons">file_download</i></button>
                    <button
                        v-if="getLoggedUser.type === 'cashier' && state!=='paid'"
                        class="btn btn-floating btn-small modal-trigger tooltipped"
                        data-tooltip="Pay"
                        data-target="invoice-pay-modal"
                        href="invoice-pay-modal"
                        @click="items(props.rowData)">
                            <i class="tiny material-icons">attach_money</i>
                    </button>
                </div>
            </vuetable>
            <vuetable-pagination class="center" ref="pagination"
                                 :css="css.pagination"
                                 @vuetable-pagination:change-page="onChangePage"
            ></vuetable-pagination>
        </div>

        <div id="invoice-pay-modal" class="modal modal-fixed-footer">
            <div class="modal-content">
                <h4>{{ invoice }}</h4>
                <div class="input-field col s4">
                    <p>NIF:
                        <input id="nif" name="nif" type="text" v-model="currentInvoice.nif">
                    </p>
                </div>
                <div class="input-field col s4">
                    <p>Name:
                        <input id="name" name="name" type="text" v-model="currentInvoice.name">
                    </p>
                </div>
                <div class="input-field col s4">
                    <p>Number of Customers:
                        <input id="num_customers" name="num_customers" type="text" v-model="currentInvoice.num_customers">
                    </p>
                </div>
                <invoice-items v-if="currentInvoice.id != undefined" :invoice="currentInvoice"></invoice-items>
                <div>
                    <p>Total: <span class="badge">{{ price(currentInvoice.total_price)}}</span></p>
                </div>
            </div>
            <div class="card-action right-align modal-footer">
                <button class="btn waves-effect waves-light grey modal-close" @click="cancel">Cancel</button>
                <button class="btn waves-effect waves-light" @click="pay">Pay</button>
            </div>
        </div>

        <div id="invoice-items-modal" class="modal modal-fixed-footer">
            <div class="modal-content">
                <h4>{{ invoice }}</h4>
                <p>NIF: {{ currentInvoice.nif }}</p>
                <p>Name: {{ currentInvoice.name }}</p>
                <invoice-items v-if="currentInvoice.id != undefined" :invoice="currentInvoice"></invoice-items>
                <div>
                    <p>Total: <span class="badge">{{ price(currentInvoice.total_price)}}</span></p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat" @click="currentInvoice= {}">Agree</a>
            </div>
        </div>
    </div>
</template>

<script>
    import Vuetable from 'vuetable-2';
    import VuetablePagination from '../components/VuetablePagination';
    import InvoiceItems from './invoice-items';
    const myScripts = require('../../../../public/js/scripts');

    export default {
        sockets: {
            invoicePendingChanged: function(message){
                this.$refs.vuetable.refresh();
            }
        },
        name: "list",
        components:{Vuetable,VuetablePagination,InvoiceItems},
        data(){
            return {
                state: "pending",
                waiters: [],
                waiter: "",
                date: "",
                currentInvoice: {},
                errors: [],
                httpOptions:{
                    headers:{
                        Authorization: axios.defaults.headers.common.Authorization
                    }
                },
                fields:[
                    {
                        name: 'state',
                        title: 'State',
                        sortField: 'state'
                    },
                    {
                        name: 'meal.waiter.name',
                        title: 'Waiter',
                        sortField: 'meal.waiter.name',
                    },
                    {
                        name: 'meal.table_number',
                        title: 'Table',
                        sortField: 'meal.table_number',
                    },
                    {
                        name: 'date',
                        title: 'Date',
                        sortField: 'date',
                    },
                    {
                        name: 'total_price',
                        title: 'Price',
                        sortField: 'total_price',
                        callback: 'price',
                    },
                    {
                        name: 'num_customers',
                        title: 'Number of Customers',
                    },
                    {
                        name: '__slot:actions',
                        title: 'Actions',
                    },
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
        methods:{
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
            setState(state){
                this.state = state;
            },
            resetFilters(){
                document.getElementsByClassName('datepicker')[0].value =
                    this.waiter = this.date = "";
                this.state = "pending";
            },
            items(invoice){
                this.currentInvoice = invoice;
            },
            download(invoice){
                axios({
                    url: '/api/invoices/'+invoice.id+'/download',
                    method: 'post',
                    responseType: 'blob', // important
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'invoice.pdf');
                    document.body.appendChild(link);
                    link.click();
                });
            },
            isAuthorized(){
                return this.$store.state.user.type === 'manager' || this.$store.state.user.type === 'cashier';
            },
            cancel(){
                console.log(this.currentInvoice);
                let request = axios.get('/api/invoices/'+this.currentInvoice.id);
                request.then(res => {
                    this.currentInvoice=res.data.data;
                });
            },
            pay(){
                axios.put('/api/invoices/'+this.currentInvoice.id, this.currentInvoice).then(res => {
                    M.toast({html: "Invoice paid"});
                    var modal = M.Modal.getInstance($('#invoice-pay-modal'));
                    modal.close();
                    this.$socket.emit("invoicePendingChanged", "invoice_paid");
                    if(this.currentInvoice.num_customers > 5){
                        this.$socket.emit("moreThan5Customers", "more_then_5_people");
                    }
                }).catch(res => {
                    this.errors = res.errors;
                    M.toast({html: "Error submitting data"});
                })
            }
        },
        computed:{
            invoice(){
                return "Invoice #"+this.currentInvoice.id;
            },
            getLoggedUser: function(){
                return this.$store.state.user;
            }
        },
        mounted: function(){
            myScripts.startInstances();
            this.$socket.emit("invoicePendingChanged", "register");
        },
        created(){
            let request = axios.get('/api/workers/waiters');
            request.then(res => this.waiters = res.data.data);
        },
        beforeDestroy(){
            this.$socket.emit("invoicePendingChanged", "unregister");
        }
    }
</script>

<style scoped>

</style>
